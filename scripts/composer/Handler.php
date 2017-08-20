<?php

namespace cjgratacos\Deployment\Composer;

use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Composer\Script\Event;

class Handler
{
  public static function createLinks(Event $event):void{

    $extra = $event->getComposer()->getPackage()->getExtra();

    if (!isset($extra['project-files-drupal-mapping']) || !is_array($extra['project-files-drupal-mapping'])) {
      throw new InvalidArgumentException("The parameter 'project-files-drupal-mapping' needs to be configured through the extra.project-files-drupal-mapping settings",1);
    }

    $provider = new Provider();
    $sourceDestMap = $provider->getFoldersList($extra['project-files-drupal-mapping']);

    $fs = new Filesystem();

    foreach ($sourceDestMap as $src=>$dest) {
      echo "Creating Link from[$src] to [$dest]".PHP_EOL;
      $fs->remove($dest);
      $fs->symlink($src,$dest);
    }
  }

  public static function siteInstallDev(Event $event):void{
    // Get extra's that are in the composer.json
    $extra = $event->getComposer()->getPackage()->getExtra();

    //  Validation that dev:db is in extra and that is a string
    if (!isset($extra['dev:db']) || !is_array($extra['dev:db'])) {
      throw new InvalidArgumentException("The parameter 'dev:db' needs to be configured through the extra.dev:db settings",1);
    }
    if (!isset($extra['drupal:site:config']) || !is_array($extra['drupal:site:config']) || !isset($extra['drupal:site:config']['password'])) {
      throw new InvalidArgumentException("The parameter 'drupal:site:config' needs to be configured through the extra.drupal:site:config.password settings",1);
    }

    // Getting the Dev DB
    $devDb = $extra['dev:db'];
    $sitePassword = $extra['drupal:site:config']['password'];
    $provider = new Provider();
    $provider->validateDBConfig($devDb);

    // Get common folders
    $drush = $provider->getDrush();
    $drupalRoot = $provider->getDrupalRoot();

    $uri = $provider->generateDBConnectionString($devDb);
    $provider->clearDbFiles($devDb);

    // Create new Process instance that is going to run the `drush si` with an sqlite db
    $process = new Process("$drush si --db-url=${uri} --account-pass=${sitePassword} -y", $drupalRoot);

    // Remove the default 60 sec timeout
    $process->setTimeout(null);
    $process->enableOutput();

    // Run Process
    $process->run(function ($type, $buffer){
      echo "INFO[$type]: ".$buffer;
    });

    if (!$process->isSuccessful()) {
      throw new ProcessFailedException($process);
    }
  }

  public static function runServer(Event $event):void{
    // Get extra's that are in the composer.json
    $extra = $event->getComposer()->getPackage()->getExtra();

    //  Validation that dev:server:port is in extra and that is a string
    if (!isset($extra['dev:server:port']) || !is_scalar($extra['dev:server:port'])) {
      throw new InvalidArgumentException("The parameter 'db-pass' needs to be configured through the extra.dev:server:port settings and it most be a scalar",1);
    }

    // Get commons
    $provider = new Provider();
    $drush = $provider->getDrush();
    $drupalRoot = $provider->getDrupalRoot();

    // Create new Process instance that is going to run the `drush rs
    $process = new Process("$drush rs ${extra['dev:server:port']} ./", $drupalRoot);

    // Remove the default 60 sec timeout
    $process->setTimeout(null);

    // Run Process
    $process->run(function ($type, $buffer){
      echo "INFO[$type]: ".$buffer;
    });

    if (!$process->isSuccessful()) {
      throw new ProcessFailedException($process);
    }
  }

  public static function backupDevServer(Event $event):void {
    // Get extra's that are in the composer.json
    $extra = $event->getComposer()->getPackage()->getExtra();

    //  Validation that db:pass is in extra and that is a string
    if (!isset($extra['dev:db']) || !is_array($extra['dev:db'])) {
      throw new InvalidArgumentException("The parameter 'dev:db' needs to be configured through the extra.dev:db settings",1);
    }

    // Get Provider
    $provider = new Provider();

    // Getting the Dev DB
    $devDb = $extra['dev:db'];

    $provider->validateDBConfig($devDb);

    $provider->backupDb($devDb);

    echo sprintf("Finished backing up `%s` db to `%s`.".PHP_EOL, $devDb['driver'],$provider->backupPath());
  }

  public static function restoreBackupDevServer(Event $event):void {
    // Get extra's that are in the composer.json
    $extra = $event->getComposer()->getPackage()->getExtra();

    //  Validation that db:pass is in extra and that is a string
    if (!isset($extra['dev:db']) || !is_array($extra['dev:db'])) {
      throw new InvalidArgumentException("The parameter 'dev:db' needs to be configured through the extra.dev:db settings",1);
    }

    // Getting the Dev DB
    $devDb = $extra['dev:db'];
    $provider = new Provider();

    $provider->restoreBackup($devDb);

    echo sprintf("Finished restoring `%s` DB Backup.".PHP_EOL, $devDb['driver']);
  }

  public static function removeBackupDevServer():void {
    $provider = new Provider();
    $provider->removeDBBackup();
  }
}

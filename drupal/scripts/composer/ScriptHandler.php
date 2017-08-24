<?php

namespace cjgratacos\Deployment\Composer;

use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Composer\Script\Event;

class ScriptHandler
{
  public static function createLinks(Event $event):void{

    $extra = $event->getComposer()->getPackage()->getExtra();

    if (!isset($extra['project-files-drupal-mapping']) || !is_array($extra['project-files-drupal-mapping'])) {
      throw new InvalidArgumentException("The parameter 'project-files-drupal-mapping' needs to be configured through the extra.project-files-drupal-mapping settings",1);
    }

    $provider = new Provider();
    $sourceDestMap = $provider->getFoldersList($extra['project-files-drupal-mapping']);

    self::link($sourceDestMap);
  }

  public static function copyDrupalSettingsFile():void {
    // Get Provider
    $provider = new Provider();
    // Get list of Drupal Site Config Files for Docker
    $drupalSiteDockerConfig =  $provider->geDockerConfigFilesToCopy();

    // Get commons
    $fs = new Filesystem();

    // Copy files from source to dest
    foreach ($drupalSiteDockerConfig as $src=>$dest) {
      if ($fs->exists($src)){
        if ($fs->exists($dest)){
          $fs->remove($dest);
        }
        echo "\e[92mMessage:\e[0m Copying [\e[93m$src\e[0m] to [\e[93m$dest\e[0m]".PHP_EOL;
        $fs->copy($src,$dest);
        $fs->chmod($dest,0750);
      }
    }

  }

  public static function siteInstallDev():void{

    // Getting the Provider
    $provider = new Provider();

    // Load .env files
    self::loadEnv($provider->envFilesCompletePaths());

    // Generate DB Config based en ENV
    $dbConfig = $provider->generateDBConfigBasedOnEnv();

    // Get drush
    $drush = $provider->getDrush();
    // Get Drupal Root Folder
    $drupalRoot = $provider->getDrupalRoot();

    // Generate DB URI
    $uri = $provider->generateDBConnectionString($dbConfig);
    // Get Drupal Site Password
    $sitePassword = $provider->getSitePasswordBasedOnEnv();

    // Create new Process instance that is going to run the `drush si` with an sqlite db
    self::runProcess("$drush si --db-url=${uri} --account-pass=${sitePassword} -y", $drupalRoot);
  }

  public static function docker(Event $event):void {
    // Getting Command Name
    $name = $event->getName();

    //Converting argument from array to a space separated string
    $cmd = implode(" ",$event->getArguments());

    // Exit if no argument was passed
    if (empty($cmd)) {
      throw new \Exception("No argument was passed to the $name command",1);
    }

    // Load the provider
    $provider = new Provider();
    $drupalRoot = $provider->getDrupalRoot();

    // Load .env files
    self::loadEnv($provider->envFilesCompletePaths());


    // Select Executor
    switch ($name){
      case 'docker:drush':
        $executor = $provider->getDrush();
        break;
      case 'docker:drupal':
        $executor = $provider->getDrupalConsole();
        break;
      default:
        throw new InvalidArgumentException("No Executor defined for $name.", 1);
    }

    self::runProcess("$executor $cmd", $drupalRoot);

  }

  public static function createEnvLink():void{
    $provider = new Provider();
    $envMap = $provider->generateEnvFileMapping();

    self::link($envMap);
  }

  private static function link(array $sourceDestMap):void {

    $fs = new Filesystem();

    foreach ($sourceDestMap as $src=>$dest) {
      echo "\e[92mMessage:\e[0m Creating Link from [\e[93m$dest\e[0m] to [\e[93m$src\e[0m]".PHP_EOL;
      $fs->remove($dest);
      $fs->symlink($src,$dest);
    }
  }

  private static function loadEnv(array $paths):void{
    $dotenv = new Dotenv();
    foreach ($paths as $path) {
      if (file_exists($path)){
        $dotenv->load($path);
      }
    }
  }

  private static function runProcess(string $cmd, string $path):void{
    // Create Process
    $process = new Process($cmd, $path, $timeout=null);

    $process->enableOutput();

    // Run Process
    $process->run(function ($type, $buffer){
      echo "d[$type]: ".$buffer;
    });

    if (!$process->isSuccessful()) {
      throw new ProcessFailedException($process);
    }
  }
}

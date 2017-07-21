<?php

namespace cjgratacos\Deployment\Composer;

use Drupal\Component\Assertion\Handle;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Composer\Script\Event;

class Handler
{
    private const BACKUP_PATH = "~/.drupal-sqlite-backup";
    private const BACKUP_FULL_PATH = Handler::BACKUP_PATH . '/backup.sqlite';

    protected static function getProjectRoot():string {
        return getcwd();
    }

    protected static function getDrupalRoot():string {
        return Handler::getProjectRoot().'/web';
    }

    protected static function getFoldersList():array {
        return [
            "themes",
            "modules"
        ];
    }

    protected static function getDrush():string {
        return Handler::getProjectRoot().'/bin/drush';
    }


    protected static function getDrupalConsole():string {
        return Handler::getProjectRoot().'/bin/drupal';
    }

    protected static function attachPrefixBasePathToFolderList(string $basePath, string $suffixFolder = null):array {

        $folders = Handler::getFoldersList();
        $suffixFolder = $suffixFolder?:'';
        return array_map(function (string $folder) use ($basePath,$suffixFolder){
            return $basePath . '/' . $folder . $suffixFolder;
        },$folders);
    }

    public static function createLinks():void{
        $sourceDestMap = array_combine(
            Handler::attachPrefixBasePathToFolderList(Handler::getProjectRoot()),
            Handler::attachPrefixBasePathToFolderList(Handler::getDrupalRoot(), '/dev')
        );

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

        //  Validation that db:pass is in extra and that is a string
        // TODO: Make extra db configurable
        if (!isset($extra['dev:db']) || !is_array($extra['dev:db'])) {
            throw new InvalidArgumentException("The parameter 'dev:db' needs to be configured through the extra.dev:db settings",1);
        }

        // Getting the Dev DB
        $dev_db = $extra['dev:db'];

        // Get common folders
        $drush = Handler::getDrush();
        $drupalRoot = Handler::getDrupalRoot();

        $fs = new Filesystem();
        $fs->remove($drupalRoot .'/'. $dev_db['path']);

        // Create new Process instance that is going to run the `drush si` with an sqlite db
        $process = new Process("$drush si --db-url=${dev_db['driver']}://${dev_db['path']} --account-pass=${dev_db['password']} -y", $drupalRoot);

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
        // TODO: Make extra db configurable
        if (!isset($extra['dev:server:port']) || !is_scalar($extra['dev:server:port'])) {
            throw new InvalidArgumentException("The parameter 'db-pass' needs to be configured through the extra.dev:server:port settings and it most be a scalar",1);
        }

        // Get common folders
        $drush = Handler::getDrush();
        $drupalRoot = Handler::getDrupalRoot();

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
        // TODO: Make extra db configurable
        if (!isset($extra['dev:db']) || !is_array($extra['dev:db'])) {
            throw new InvalidArgumentException("The parameter 'dev:db' needs to be configured through the extra.dev:db settings",1);
        }

        // Getting the Dev DB
        $dev_db = $extra['dev:db'];

        if ($dev_db['driver'] !== "sqlite") {
            throw new InvalidArgumentException("Only supporting SQLite backup at the moment.",1);
        }

        $fs = new Filesystem();

        $drupalRoot = Handler::getDrupalRoot();

        $fs->remove(Handler::BACKUP_PATH);
        $fs->mkdir(Handler::BACKUP_PATH);
        $fs->copy($dev_db['path'], Handler::BACKUP_FULL_PATH);

        echo "Finished backing up ${dev_db['path']} to ".Handler::BACKUP_FULL_PATH ;
    }

    public static function restoreBackupDevServer(Event $event):void {
        // Get extra's that are in the composer.json
        $extra = $event->getComposer()->getPackage()->getExtra();

        //  Validation that db:pass is in extra and that is a string
        // TODO: Make extra db configurable
        if (!isset($extra['dev:db']) || !is_array($extra['dev:db'])) {
            throw new InvalidArgumentException("The parameter 'dev:db' needs to be configured through the extra.dev:db settings",1);
        }

        // Getting the Dev DB
        $dev_db = $extra['dev:db'];

        if ($dev_db['driver'] !== "sqlite") {
            throw new InvalidArgumentException("Only supporting SQLite restore at the moment.",1);
        }

        $fs = new Filesystem();

        if ($fs->exists(Handler::BACKUP_FULL_PATH)) {
            throw new FileException("File ".Handler::BACKUP_FULL_PATH." doesn't exist, unable to restore backup",1);
        }

        $drupalRoot = Handler::getDrupalRoot();

        $fs->copy(Handler::BACKUP_FULL_PATH, $dev_db['path']);
        $fs->remove(Handler::BACKUP_PATH);

        echo "Finished restoring ". Handler::BACKUP_FULL_PATH." to ${dev_db['path']}";
    }

    public static function removeBackupDevServer():void {
        $fs = new Filesystem();
        $fs->remove(Handler::BACKUP_PATH);
        if ($fs->exists(Handler::BACKUP_FULL_PATH)) {
            $fs->remove(Handler::BACKUP_PATH);
            echo "Backup found and removed";
        } else {
            echo "No backup was found";
        }
    }
}

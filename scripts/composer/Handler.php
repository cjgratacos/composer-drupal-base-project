<?php

namespace cjgratacos\Deployment\Composer;

use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Composer\Script\Event;

class Handler
{

    static protected function getProjectRoot():string {
        return getcwd();
    }

    static protected function getDrupalRoot():string {
        return Handler::getProjectRoot().'/web';
    }

    static protected function getFoldersList():array {
        return [
            "themes",
            "modules"
        ];
    }

    static protected function getDrush():string {
        return Handler::getProjectRoot().'/vendor/bin/drush';
    }


    static protected function getDrupalConsole():string {
        return Handler::getProjectRoot().'/vendor/bin/drupal';
    }

    static protected function attachPrefixBasePathToFolderList(string $basePath, string $suffixFolder = null):array {

        $folders = Handler::getFoldersList();
        $suffixFolder = $suffixFolder?:'';
        return array_map(function (string $folder) use ($basePath,$suffixFolder){
            return $basePath . '/' . $folder . $suffixFolder;
        },$folders);
    }

    static public function createLinks():void{
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

    static public function siteInstallDev(Event $event):void{
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
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        echo $process->getOutput();
    }

    static public function runServer(Event $event):void{
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
            if (Process::ERR === $type) {
                echo "ERROR: ".$buffer;
            } else {
                echo "INFO: ".$buffer;
            }
        });

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        echo $process->getOutput();
    }
}

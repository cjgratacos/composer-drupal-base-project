<?php

namespace cjgratacos\Deployment\Composer;

use Symfony\Component\Filesystem\Filesystem;
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
            $fs->symlink($src,$dest);
        }
    }
}

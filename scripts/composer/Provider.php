<?php

namespace cjgratacos\Deployment\Composer;

use Composer\Script\Event;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Exception\InvalidArgumentException;

class Provider {

  /**
   * @var string
   */
  private $projectRoot;

  /**
   * @var string
   */
  private $backupPath;

  /**
   * @var string
   */
  private $drupalRoot;

  /**
   * @var string
   */
  private $binDir;

  /**
   * Provider constructor.
   */
  public function __construct() {
    // Project root
    $this->projectRoot = getcwd().'/drupal';
    // Backup Path
    $this->backupPath = "drupal-backup";
    // Drupal Root Folder Name
    $this->drupalRootFolderName = './drupal/web';
    // Drupal Root Directory
    $this->drupalRoot = $this->projectRoot . $this->drupalRootFolderName;
    // Project bin directory
    $this->binDir = $this->projectRoot.'/bin';
  }

  /**
   * @return string
   */
  public function getProjectRoot():string {
    return $this->projectRoot;
  }

  /**
   * @return string
   */
  public function backupPath():string{
    return $this->backupPath;
  }

  /**
   * @return string
   */
  public function getDrupalRoot():string {
    return $this->drupalRoot;
  }

  /**
   * @return string
   */
  public function getDrush():string {
    return $this->getBin('drush');
  }

  /**
   * @return string
   */
  public function getDrupalConsole():string {
    return $this->getBin('drupal');
  }

  /**
   * @param array $folderArray
   *
   * @return array
   */
  public function getFoldersList(array $folderArray):array {

    return $this->generateSystemToDrupalFolderMapping($folderArray);
  }

  /**
   * @param array $dbConfig
   *
   * @return string
   */
  public function generateDBConnectionString(array $dbConfig):string {
    switch ($dbConfig['driver']){
      case 'sqlite':

        return "${$dbConfig['driver']}://${$dbConfig['path']}";
        break;
      case 'mysql':
        return "${$dbConfig['driver']}://${$dbConfig['username']}:${$dbConfig['password']}@${$dbConfig['host']}:${$dbConfig['port']}/${$dbConfig['name']}";
        break;
      default:
        throw new InvalidArgumentException("Invalid Driver, can install site only with sqlite or mysql drivers.",1);
    }
  }

  /**
   * @param array $dbConfig
   */
  public function clearDbFiles(array $dbConfig):void{
    if ($dbConfig['driver'] == 'sqlite') {
      $fs->remove($this->drupalRoot .'/'. $dbConfig['path']);
    }
  }

  /**
   * @param array $dbConfig
   */
  public function validateDBConfig(array $dbConfig):void {

    if (!isset($dbConfig['driver'])){
      throw new InvalidArgumentException("No driver in extra.dev:db.driver settings.",1);
    }
    switch ($dbConfig['driver']) {
      case 'sqlite':
        if (!isset($dbConfig['path'])){
          throw new InvalidArgumentException("Invalid Driver, can install site only with sqlite or mysql drivers.",1);
        }
        break;
      case 'mysql':
        $arr = ['password', 'username', 'password', 'host', 'port', 'name'];
        array_walk($arr, function($value) use ($dbConfig) {
          if (!array_key_exists($value,$dbConfig)) {
            throw new InvalidArgumentException("'$value' doesn't exist in extra.dev:db.$value settings.",1);
          }
        });
        break;
      default:
        throw new InvalidArgumentException("Invalid Driver, can install site only with sqlite or mysql drivers.",1);
        break;
    }
  }

  /**
   * @param array $dbConfig
   */
  public function backupDb(array $dbConfig):void{
    $this->regenerateBackupFolder();
    switch ($dbConfig['driver']){
      case 'sqlite':
        $this->copyFiles($dbConfig['path'], $this->backupPath . '/backup.sqlite');
        break;
      case 'mysql':
        $this->backupMysql($dbConfig);
        break;
      default:
        throw new InvalidArgumentException("Only supporting SQLite or MySQL backups at the moment.",1);
    }
  }

  /**
   * Remove DB Backup
   */
  public function removeDBBackup():void{
    $fs = new Filesystem();
    $fs->remove($this->backupPath);
  }

  /**
   *  Restore DB Backup
   * @param array $dbConfig
   */
  public function restoreBackup(array $dbConfig):void{

    $this->checkFolderEmpty();

    switch ($dbConfig['driver']){
      case 'sqlite':
        $this->copyFiles("$this->backupPath/backup.sqlite", $dbConfig['path']);
        break;
      case 'mysql':
        $process = new Process("mysql  --user=${$dbConfig['username']} --password=${$dbConfig['password-db']} --host=${$dbConfig['host-db']} --port=${$dbConfig['port-db']} --database=${$dbConfig['name-db']} < backup.sql", $this->backupPath);
        $process->setTimeout(null);
        $process->run();
        break;
      default:
        throw new InvalidArgumentException("Only supporting SQLite or MySQL backups at the moment.",1);
    }

  }

  private function checkFolderEmpty():void{
    $backupFolder = $this->projectRoot.'/'.$this->backupPath;

    if (!file_exists($backupFolder) && count(scandir($backupFolder)) == 2) {
      throw new FileException("Folder $this->backupPath is empty or doesn't exist, unable to restore backup",1);
    }
  }

  private function regenerateBackupFolder():void{
    $fs = new Filesystem();
    $fs->remove($this->backupPath);
    $fs->mkdir($this->backupPath);
    $fs->chmod($this->backupPath, 0755);

  }

  private function copyFiles(string $currentPath, string $destPath):void{
    $fs = new Filesystem();
    $fs->copy($currentPath, $destPath);
  }

  private function backupMysql(array $dbConfig):void{

    $process = new Process("mysqldump  --user=${$dbConfig['username']} --password=${$dbConfig['password']} --host=${$dbConfig['host']} --port=${$dbConfig['port']} --result-file=backup.sql ${$dbConfig['name']}",self::backupPath());
    $process->setTimeout(null);

    $process->run(function ($type, $buffer){
      echo "INFO[$type]: ".$buffer;
    });

    if (!$process->isSuccessful()) {
      throw new ProcessFailedException($process);
    }
  }

  private function getBin(string $executable):string {
    return $this->binDir. "/$executable";
  }

  private function generateSystemToDrupalFolderMapping(array $folderArray):array {
    $map = [];

    foreach ($folderArray as $folderMapKey=>$folderMapArr){
      if ($folderMapArr == null) {
        echo "$folderMapKey was not set, since it doesn't have a mapping structure define.".PHP_EOL;
        break;
      } elseif(!isset($folderMapArr['project']) || !isset($folderMapArr['drupal'])){
        echo "'project' and 'drupal' key most be defined in the $folderMapKey composer node.".PHP_EOL;
        break;
      }

      $map[$folderMapArr['project']] = $this->drupalRootFolderName.$folderMapArr['drupal'];
    }

    return $map;
  }

  private function attachPrefixBasePathToFolderMap(string $basePath, array $folderMap):array {
    $arr = [];
    foreach ($folderMap as $src=>$dest) {
      echo "$src=>$dest | ";
      $arr[$basePath.$src] = $basePath.$dest;
    }
    return $arr;
  }
}
<?php

namespace cjgratacos\Deployment\Composer;

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
   * @var string
   */
  private $templateFolder;

  /**
   * @var string
   */
  private $envPaths;

  /**
   * Provider constructor.
   */
  public function __construct() {
    // Project root
    $this->projectRoot = getcwd();
    // Backup Path
    $this->backupPath = "drupal-backup";
    // Drupal Root Folder Name
    $this->drupalRootFolderName = '/web';
    // Drupal Root Directory
    $this->drupalRoot = $this->projectRoot . $this->drupalRootFolderName;
    // Project bin directory
    $this->binDir = $this->projectRoot.'/bin';
    // Template folder
    $this->templateFolder = $this->projectRoot . '/scripts/composer/templates';

    // ENV Paths
    $this->envPaths = [
      'LOCAL'=>'.local.env',
      'PROD'=>'.prod.env'
    ];
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
   * @return array
   */
  public function envFilesCompletePaths(): array {
    $arr = [];
    foreach ($this->envPaths as $key=>$val) {
      $arr[]="$this->projectRoot/$val";
    }
    return $arr;
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
        return sprintf("%s://%s",$dbConfig['driver'],$dbConfig['path']);
        break;
      case 'mysql':
        return sprintf("%s://%s:%s@%s:%d/%s", $dbConfig['driver'], $dbConfig['username'],$dbConfig['password'], $dbConfig['host'], $dbConfig['port'], $dbConfig['name']);
        break;
      default:
        throw new InvalidArgumentException("Invalid Driver, can install site only with sqlite or mysql drivers.",1);
    }
  }

  public function generateDBConfigBasedOnEnv():array {

    if (!isset($_ENV['DB_DRIVER'])){
      throw new InvalidArgumentException("No driver in \$_ENV['DB_DRIVER'].",1);
    }
    switch ($_ENV['DB_DRIVER']) {
      case 'sqlite':
        if (!isset($_ENV['SQLITE_PATH'])){
          throw new InvalidArgumentException("No Path define in \$_ENV['SQLITE_PATH'].",1);
        }
        $config = [
          'driver'=>$_ENV['DB_DRIVER'],
          'path'=>$_ENV['SQLITE_PATH']
        ];
        break;
      case 'mysql':
        $arr = ['DB_HOST', 'DB_USER', 'DB_PASS', 'DB_PORT', 'DB_NAME'];
        array_walk($arr, function($value) {
          if (!array_key_exists($value,$_ENV)) {
            throw new InvalidArgumentException("'$value' doesn't exist in \$_ENV settings.",1);
          }
        });
        $config = [
          'driver'=>$_ENV['DB_DRIVER'],
          'username'=>$_ENV['DB_USER'],
          'password'=>$_ENV['DB_PASS'],
          'host'=>$_ENV['DB_HOST'],
          'port'=>$_ENV['DB_PORT'],
          'name'=>$_ENV['DB_NAME'],
        ];
        break;
      default:
        throw new InvalidArgumentException("Invalid Driver in \$_ENV['DB_DRIVER'], can install site only with sqlite or mysql drivers.",1);
        break;
    }
    return $config;
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

      $map[$folderMapArr['project']] = $folderMapArr['drupal'];
    }

    return $map;
  }

  public function geDockerConfigFilesToCopy(): array  {
    return [
      $this->templateFolder . '/services.yml' => $this->drupalRoot .'/sites/default/services.yml',
      $this->templateFolder . '/settings.php' => $this->drupalRoot .'/sites/default/settings.php'
    ];
  }

  public function generateEnvFileMapping():array {
    if (file_exists($this->envPaths['PROD'])) {
     $env = $this->envPaths['PROD'];
    } elseif (file_exists($this->envPaths['LOCAL'])) {
      $env = $this->envPaths['LOCAL'];
    } else {
      throw new \Exception("No .env defined in the root project");
    }

    return [
      "../../../$env" => ".$this->drupalRootFolderName/sites/default/.env"
    ];
  }
  public function getSitePasswordBasedOnEnv():string {
    if (!isset($_ENV['DRUPAL_PASS'])) {
      throw new InvalidArgumentException("\$_ENV['DRUPAL_PASS'] is not defined.",1);
    }
    return $_ENV['DRUPAL_PASS'];
  }
}
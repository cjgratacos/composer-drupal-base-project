<?php

if (file_exists(getcwd() . '/.env')){
  require_once __DIR__ . '/../../autoload.php';
  $dotEnv = new Symfony\Component\Dotenv\Dotenv();
  $dotEnv->load(getcwd() . '/.env');
}

$config_directories = [
  'sync' => getenv('DRUPAL_SYNC_FOLDER')
];

$databases['default']['default'] = [
  'database' => getenv('DB_NAME'),
  'username' => getenv('DB_USER'),
  'password' => getenv('DB_PASS'),
  'host' => getenv('DB_HOST'),
  'port' => getenv('DB_PORT'),
  'prefix' => '',
  'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
  'driver' => 'mysql'
];

$settings = [
  'hash_salt' => getenv('DRUPAL_HASH_SALT'),
  'update_free_access' => false,
  'container_yamls' => [
    __DIR__ . '/services.yml'
  ],
  'install_profile' => getenv('DRUPAL_PROFILE'),
  'vaultURL' => getenv('VAULT_URL'),
  'vaultAppId' => getenv('VAULT_APPID'),
  'vaultUserId' => getenv('VAULT_USERID'),
  'vaultName' => getenv('VAULT_NAME')
];

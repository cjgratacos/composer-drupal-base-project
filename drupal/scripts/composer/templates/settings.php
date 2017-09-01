<?php

if (file_exists(__DIR__ . '/.env')){
  require_once __DIR__ . '/../../autoload.php';
  $dotEnv = new Symfony\Component\Dotenv\Dotenv();
  $dotEnv->load(__DIR__ . '/.env');
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
];

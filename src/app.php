<?php

use Silex\Application;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\HttpFragmentServiceProvider;
use ORM\Provider\DoctrineORMServiceProvider;

$app = new Application();
$app->register(new ValidatorServiceProvider());
$app->register(new ServiceControllerServiceProvider());
$app->register(new HttpFragmentServiceProvider());

 $provider = array(
    'dbs.options' => array (
        'api' => array(
            'driver'    => 'pdo_pgsql',
            'host'      => '127.0.0.1',
            'dbname'    => 'maestroapi',
            'user'      => 'dtic',
            'password'  => 'dtic',
        ),
        'establecimiento' => array(
            'driver'    => 'pdo_pgsql',
            'host'      => '127.0.0.1',
            'dbname'    => 'e',
            'user'      => 'dtic',
            'password'  => 'dtic',
        ),
        'insumo' => array(
            'driver'    => 'pdo_pgsql',
            'host'      => '127.0.0.1',
            'dbname'    => 'mi',
            'user'      => 'postgres',
            'password'  => 'root',
        ),
        'consumo' => array(
            'driver'    => 'pdo_pgsql',
            'host'      => '127.0.0.1',
            'dbname'    => 'cc',
            'user'      => 'dtic',
            'password'  => 'dtic',
        ),
        'sinab' => array(
            'driver'    => 'PDO_SQLSRV',
            'host'      => '192.168.1.200',
            'port'      => '1433',
            'dbname'    => 'abastecimiento',
            'user'      => 'sa',
            'password'  => 'passwd',
        ),
    ),
 );
 
 $app['swiftmailer.options'] = array(
    'host' => 'smtp.gmail.com',
    'port' => '465',
    'username' => 'pruebamail503@gmail.com',
    'password' => '18001800Unix',
    'encryption' => 'ssl',
    'auth_mode' => 'login'
 );
 
 $app->register(new Silex\Provider\DoctrineServiceProvider(), $provider);
 $app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => __DIR__.'/development.log',
 ));
 $app->register(new Silex\Provider\SwiftmailerServiceProvider());
 

 
 $app['autentica'] = $app->protect(function ($app, $tocken) {
	 $sql = "SELECT * FROM mnt_acceso WHERE tocken_acceso = '$tocken'"; 
	 $acceso = $app['dbs']['api']->executeQuery($sql)->rowCount();
	 if ($acceso > 0)
		return true;
	 else
		return false;
 });

 require __DIR__.'/routes.php';
 
return $app;

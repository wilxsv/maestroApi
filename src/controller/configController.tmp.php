<?php

 /*
  * Configuraciones generales de la aplicacion
  *
  */

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
 $app->register(new Silex\Provider\DoctrineServiceProvider(), $provider);
 
 $app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => __DIR__.'/development.log',
 ));
 
 $app['swiftmailer.options'] = array(
    'host' => '',
    'port' => '',
    'username' => '',
    'password' => '',
    'encryption' => 'ssl',
    'auth_mode' => 'login'
 );
 
 $app['autentica'] = $app->protect(function ($app, $tocken) {
	 $sql = "SELECT * FROM mnt_acceso WHERE tocken_acceso = '$tocken'"; 
	 $acceso = $app['dbs']['api']->executeQuery($sql)->rowCount();
	 if ($acceso > 0)
		return true;
	 else
		return false;
 });

?>

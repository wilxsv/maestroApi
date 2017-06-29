<?php

 /*
  * Configuraciones generales de la aplicacion
  *
  */
  
 $app->register(new Silex\Provider\DoctrineServiceProvider(), array(
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
    'charset'  => 'utf8',
    'driverOptions' => array(
        1002 => 'SET NAMES utf8'
    )
        ),
        'insumo' => array(
            'driver'    => 'pdo_pgsql',
            'host'      => '127.0.0.1',
            'dbname'    => 'maestroinsumo',
            'user'      => 'dtic',
            'password'  => 'dtic',
    'charset'  => 'utf8',
    'driverOptions' => array(
        1002 => 'SET NAMES utf8'
    )
        ),
        'sinab' => array(
            'driver'    => 'pdo_sqlsrv',
            'host'      => '127.0.0.1',
            'dbname'    => 'abastecimiento',
            'user'      => 'sa',
            'password'  => 'passwd',
    'charset'  => 'utf8',
    'driverOptions' => array(
        1002 => 'SET NAMES utf8'
    )
        ),
    ),
 ));
 
 $app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => __DIR__.'/development.log',
 ));
 
 $app['swiftmailer.options'] = array(
    'host' => 'smtp.gmail.com',
    'port' => '465',
    'username' => 'barandigoyen@gmail.com',
    'password' => '1800Unix',
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

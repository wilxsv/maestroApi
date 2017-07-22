<?php

 require_once __DIR__.'/../vendor/autoload.php';
 
 $filename = __DIR__.preg_replace('#(\?.*)$#', '', $_SERVER['REQUEST_URI']);
 if (php_sapi_name() === 'cli-server' && is_file($filename)) {
    return false;
 }

 use Symfony\Component\HttpFoundation\Request;
 use Symfony\Component\HttpFoundation\Response;
 use Doctrine\Common\ClassLoader;
 use Symfony\Component\HttpFoundation\ParameterBag;
 
 
 $app = new Silex\Application();
 $app['debug'] = true;
 $uri = '/v1/';
 
 require_once __DIR__.'/../src/controller/configController.php';
 require_once __DIR__.'/../src/controller/infoController.php';
 require_once __DIR__.'/../src/controller/establecimientoController.php';
 require_once __DIR__.'/../src/controller/insumoController.php';
 require_once __DIR__.'/../src/controller/sinabController.php';
 
 $app->mount($uri.'info', $info);
 $app->mount($uri.'establecimientos', $establecimiento);
 $app->mount($uri.'insumos', $insumo);
 $app->mount($uri.'sinab', $sinab);
 
  $app->get('/', function () use ($app) {
	 
    return $app->json(array('respuesta' => 'Estamos activos, que le podemos responder'), 201);
 }); 
 $app->run();
?>

	

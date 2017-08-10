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

 $app->before(function (Request $request) {
    if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());
    }
 });

 require_once __DIR__.'/../src/controller/configController.php';
 require_once __DIR__.'/../src/controller/infoController.php';
 require_once __DIR__.'/../src/controller/establecimientoController.php';
 require_once __DIR__.'/../src/controller/insumoController.php';
 require_once __DIR__.'/../src/controller/sinabController.php';
 require_once __DIR__.'/../src/controller/consumoController.php';

 $app->post($uri.'consumos'.'/siaps', function (Request $request) use ($app) {

    $data = array(
        'tocken' => $request->request->get('tocken'),
        'establecimiento' => $request->request->get('establecimiento'),
        'producto'  => $request->request->get('producto'),
        'cantidadexistencia'  => $request->request->get('cantidadexistencia'),
        'cantidadconsumo'  => $request->request->get('cantidadconsumo'),
        'fecha'  => $request->request->get('fecha'),
        'user'  => $request->request->get('user'),
        'lote'  => $request->request->get('lote'),
        'almacen'  => $request->request->get('almacen'),
        'caducidad'  => $request->request->get('caducidad'),
    );
    $error = array('error' => 'No pude completar tu peticion.');
	$acceso = $app['autentica'];
	if (!$acceso($app, $data['tocken'])){ return $app->json($error, 404); }
	$var = $app['registraConsumo'];
	$post = $var($app, $data);

    return $app->json($post, 201);
 });
 
 
 $app->mount($uri.'info', $info);
 $app->mount($uri.'establecimientos', $establecimiento);
 $app->mount($uri.'insumos', $insumo);
 $app->mount($uri.'suministros', $insumo);
 $app->mount($uri.'sinab', $sinab);
 $app->mount($uri.'consumos', $consumo);
 
 $app->get('/', function () use ($app) {
	 
    return $app->json(array('respuesta' => 'Estamos activos, que le podemos responder'), 201);
 }); 
 $app->run();
?>

	

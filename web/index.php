<?php

 require_once __DIR__.'/../vendor/autoload.php';
 require_once __DIR__.'/../seguridad.php';
 require_once __DIR__.'/../insumo.php';
 require_once __DIR__.'/../establecimiento.php';
 require_once __DIR__.'/../consumo.php';
 
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

 $app->get($uri.'feedback', function (Request $request) {
    $message = $request->get('message');
    mail('feedback@yoursite.com', '[YourSite] Feedback', $message);

    return new Response('Thank you for your feedback!', 201);
 });

 $app->get($uri.'getSchemas/{tocken}/{maestro}', function ($tocken, $maestro) use ($app) {
	 $error = array('respuesta' => 'Servicio no autorizado.');
	 if (!autentica( $tocken )){ return $app->json($error, 404); }
    return $app->json(array('respuesta' => esquemas($maestro)), 201);
 });
 
 $app->get($uri.'getServicios/{tocken}', function ($tocken) use ($app) {
	 $error = array('respuesta' => 'Servicio no autorizado.');
	 if (!autentica( $tocken )){ return $app->json($error, 404); }
    return $app->json(array('respuesta' => servicios()), 201);
 });

 $app->get($uri.'getEstablecimiento/{tocken}/{id}/{value}', function ($tocken, $id, $value) use ($app) {
	 if (true){ return $app->json($error, 404); }
    return $app->json(array('respuesta' => establecimientos()), 201);
 });
 $app->get($uri.'getMaestro/{tocken}/establecimiento.json', function ($tocken) use ($app) {
	 if (!autentica( $tocken )){ return $app->json(array('respuesta' => 'Servicio no autorizado.'), 404); }
    return $app->json(array('respuesta' => establecimientosAll()), 201);
 });

 $app->post($uri.'consumo/posts', function (Request $request) use ($app) {
	$post['respuesta']=array('respuesta' => 'No se registro el consumo.'); 
	$post['codigo'] = 404; 
    if ($request) { 
		 $data = json_decode($request->getContent(), true); 
		 $post['respuesta']=$data; 
		 $post['codigo'] = 201;
	 }

    return $app->json(array('respuesta' => $post['respuesta']), $post['codigo']);
 });

 $app->run();
/*curl http://localhost:8080/api/v1/consumo/posts -d '{"tocken":"passw","datos":[{}]}' -H 'Content-Type: application/json'*/
?>

	

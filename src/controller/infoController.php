<?php

 /*
  * Consultas a servicios generales que responde la API
  */
 $info = $app['controllers_factory'];


 //Listado de servicios, estatico hasta la fecha, dinamico desde la base cuando se generen los controlladores
 $info->get('/servicios', function () use ($app) {
	 $tocken = $_GET["tocken"];
	 $error = array('respuesta' => 'Servicio no autorizado.');
	 $acceso = $app['autentica'];
	 if (!$acceso($app, $_GET["tocken"])){ return $app->json($error, 404); }
	 $rest = info_servicios();
    return $app->json(array('respuesta' => $rest['respuesta']), $rest['codigo']);
 });
 
  function info_servicios( ){
	 $array['respuesta']=array("info", "establecimientos", "suministros", "consumos");
	 $array['codigo'] = 201; 
	 return $array;
 } 
 /*
 //Listado de servicios, estatico hasta la fecha, dinamico desde la base cuando se generen los controlladores
 $app->post($uri.'info/enviar', function () use ($app) {
	 $tocken = $_POST["tocken"];
	 $maestro = $_POST["maestro"];
	 $esquema = $_POST["esquema"];
	 $id = $_POST["id"];
	 $error = array('respuesta' => 'Servicio no autorizado.');
	 if (!autentica( $tocken, 'info' )){ return $app->json($error, 404); }
	 $rest = info_enviar();
    return $app->json(array('respuesta' => $rest['respuesta']), $rest['codigo']);
 });
 $app->post($uri.'info/notificar', function () use ($app) {
	 $tocken = $_GET["tocken"];
	 $destino = $_GET["destino"];
	 $asunto = $_GET["asunto"];
	 $mensaje = $_GET["mensaje"];
	 $adjunto = $_GET["adjunto"];
	 $error = array('respuesta' => 'Servicio no autorizado.');
	 if (!autentica( $tocken, 'info' )){ return $app->json($error, 404); }
	 $rest = info_notificar();
    return $app->json(array('respuesta' => $rest['respuesta']), $rest['codigo']);
 });*/
?>

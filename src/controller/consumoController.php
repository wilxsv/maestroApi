<?php

 /*
  * Consultas a servicios relacionados al sistema de consumos y existencias
  *
  */
 $consumo = $app['controllers_factory'];
 
 //ingreso de consumos y existencias
 $consumo->post('/', function () use ($app) {
	 $tocken = $_GET["tocken"];
	 
	 $acceso = $app['autentica'];
	 if (!$acceso($app, $_GET["tocken"])){ return $app->json($error, 404); }
	 
	 return $app->json(array('respuesta' => $array), 201);
 }); 
 
?>

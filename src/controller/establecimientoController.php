<?php

 /*
  * Consultas a servicios generales
  *
  */
 $maestro_data = 
 $establecimiento = $app['controllers_factory'];
 
 //Listado de establecimientos
 $establecimiento->get('/todos.json', function () use ($app) {
	 $tocken = $_GET["tocken"];
	 
	 $acceso = $app['autentica'];
	 if (!$acceso($app, $_GET["tocken"])){ return $app->json($error, 404); }
	 $sql = "SELECT e.nombre, split_part(e.path, '/', 2)  as region, split_part(e.parent, '/', 3)  as sibasi, e.id, e.parent_id, e.id_tipo_establecimiento, e.idmicrored
			FROM (
				WITH RECURSIVE path(nombre, path, parent, id, parent_id, id_tipo_establecimiento, idmicrored) AS (
					SELECT nombre, '/', NULL, id, id_establecimiento_padre, id_tipo_establecimiento, idmicrored FROM ctl_establecimiento WHERE id = 1038 AND enable_schema = 1
					UNION
					SELECT ctl_establecimiento.nombre, parentpath.path || CASE parentpath.path WHEN '/' THEN '' ELSE '/' END || ctl_establecimiento.nombre, parentpath.path, ctl_establecimiento.id, ctl_establecimiento.id_establecimiento_padre, ctl_establecimiento.id_tipo_establecimiento, ctl_establecimiento.idmicrored
					FROM ctl_establecimiento, path as parentpath
					WHERE ctl_establecimiento.id_establecimiento_padre = parentpath.id
				)
				SELECT * FROM path
			) AS e"; 
	 $array = $app['dbs']['establecimiento']->fetchAll($sql);
	 return $app->json(array('respuesta' => $array), 201);
 }); 
 
 //Listado de establecimientos
 $establecimiento->get('/', function () use ($app) {
	 $tocken = $_GET["tocken"];
	 $acceso = $app['autentica'];
	 if (!$acceso($app, $_GET["tocken"])){ return $app->json($error, 404); }
	 $sql = "SELECT e.nombre, split_part(e.path, '/', 2)  as region, split_part(e.parent, '/', 3)  as sibasi, e.id, e.parent_id, e.id_tipo_establecimiento, e.idmicrored
			FROM (
				WITH RECURSIVE path(nombre, path, parent, id, parent_id, id_tipo_establecimiento, idmicrored) AS (
					SELECT nombre, '/', NULL, id, id_establecimiento_padre, id_tipo_establecimiento, idmicrored FROM ctl_establecimiento WHERE id = 1038 AND enable_schema = 1
					UNION
					SELECT ctl_establecimiento.nombre, parentpath.path || CASE parentpath.path WHEN '/' THEN '' ELSE '/' END || ctl_establecimiento.nombre, parentpath.path, ctl_establecimiento.id, ctl_establecimiento.id_establecimiento_padre, ctl_establecimiento.id_tipo_establecimiento, ctl_establecimiento.idmicrored
					FROM ctl_establecimiento, path as parentpath
					WHERE ctl_establecimiento.id_establecimiento_padre = parentpath.id
				)
				SELECT * FROM path
			) AS e"; 
	 $array = $app['dbs']['establecimiento']->fetchAll($sql);
	 return $app->json(array('respuesta' => $array), 201);
 });

//Listado de tipos de establecimientos oficialmente reconocidos por el ministerio
 $establecimiento->get('/tipos', function () use ($app) {
	 $tocken = $_GET["tocken"];
	 $acceso = $app['autentica'];
	 if (!$acceso($app, $_GET["tocken"])){ return $app->json($error, 404); }
	 $sql = "SELECT id, nombre, codigo FROM ctl_tipo_establecimiento"; 
	 $array = $app['dbs']['establecimiento']->fetchAll($sql);
	 return $app->json(array('respuesta' => $array), 201);
 });

//Listado de establecimientos por años de apertura
 $establecimiento->get('/apertura', function () use ($app) {
	 $tocken = $_GET["tocken"];
	 $min = '';
	 $max = '';
	 if ( !empty($_GET['min']) && is_numeric($_GET['min']) ){
		 $min = 'AND anio_apertura >= '.$_GET['min'];
	 }
	 if ( !empty($_GET['max']) && is_numeric($_GET['max']) ){
		 $max = 'AND anio_apertura <= '.$_GET['max'];
	 }
	 $acceso = $app['autentica'];
	 if (!$acceso($app, $_GET["tocken"])){ return $app->json($error, 404); }
	 $select = "id, id_tipo_establecimiento, nombre, direccion, telefono, fax, latitud, longitud, anio_apertura, activo, idmicrored, poblacion_asignana, cantidad_familia, enable_schema";
	 $sql = "SELECT $select FROM ctl_establecimiento WHERE enable_schema = 1 $min $max"; 
	 $array = $app['dbs']['establecimiento']->fetchAll($sql);
	 return $app->json(array('respuesta' => $array), 201);
 });

//Listado de establecimientos por nivel administrativo del minsal
 $establecimiento->get('/nivelminsal', function () use ($app) {
	 $tocken = $_GET["tocken"];
	 $acceso = $app['autentica'];
	 if (!$acceso($app, $_GET["tocken"]) && !empty($_GET['ids']) ){ return $app->json($error, 404); }
	 $arr = explode(',', $_GET['ids']);
	 foreach ($arr as $val) {
		 if (!is_numeric($val))
		 return $app->json(array('error' => 'No interpreto bien tu pregunta.'), 404);
	 }
	 $select = "id, id_tipo_establecimiento, nombre, direccion, telefono, fax, latitud, longitud, anio_apertura, activo, idmicrored, poblacion_asignana, cantidad_familia, enable_schema";
	 $ids = $_GET['ids'];
	 $sql = "SELECT $select FROM ctl_establecimiento WHERE enable_schema = 1 AND id_cat_nivel_minsal IN ($ids)"; 
	 $array = $app['dbs']['establecimiento']->fetchAll($sql);
	 return $app->json(array('respuesta' => $array), 201);
 });

//Listado de micro redes
 $establecimiento->get('/microred', function () use ($app) {
	 $tocken = $_GET["tocken"];
	 $acceso = $app['autentica'];
	 if (!$acceso($app, $_GET["tocken"])){ return $app->json($error, 404); }
	 $sql = "SELECT id, nombre, activo, sibasi_id AS establecimiento_cabeza FROM ctl_microred"; 
	 $array = $app['dbs']['establecimiento']->fetchAll($sql);
	 return $app->json(array('respuesta' => $array), 201);
 });

//Listado de esquemas de informacion 
 $establecimiento->get('/esquemas', function () use ($app) {
	 $tocken = $_GET["tocken"];
	 $acceso = $app['autentica'];
	 if (!$acceso($app, $_GET["tocken"])){ return $app->json($error, 404); }
	 $sql = "SELECT schema_name FROM information_schema.schemata WHERE schema_name NOT LIKE 'p%'and schema_name <> 'information_schema'"; 
	 $array = $app['dbs']['establecimiento']->fetchAll($sql);
	 return $app->json(array('respuesta' => $array), 201);
 });

?>

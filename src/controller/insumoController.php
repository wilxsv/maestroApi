<?php

 /*
  * Consultas a servicios relacionados a establecimientos
  *
  */
 $insumo = $app['controllers_factory'];
 
 //Listado de productos
 $insumo->get('/todos.json', function () use ($app) {
	 $tocken = $_GET["tocken"];
	 
	 $acceso = $app['autentica'];
	 if (!$acceso($app, $_GET["tocken"])){ return $app->json($error, 404); }
	 $select = "id, codigo_sinab, codigo_nu, grupoid, nombre_largo_insumo, ctl_nivel_usoid AS nivel_uso, ctl_presentacionid AS presentacion_id, listado_oficial";
	 $sql = "SELECT $select FROM ctl_insumo WHERE enable_schema = 1";
	 $array = $app['dbs']['insumo']->fetchAll($sql);
	 return $app->json(array('respuesta' => $array), 201);
 }); 
 
 //Listado de productos
 $insumo->get('/', function () use ($app) {
	 $tocken = $_GET["tocken"];
	 $acceso = $app['autentica'];
	 if (!$acceso($app, $_GET["tocken"])){ return $app->json($error, 404); }
	 $idPrograma = '';
	 if ( !empty($_GET['idPrograma']) && is_numeric($_GET['idPrograma']) ){
		 $idPrograma = 'AND ctl_programaid = '.$_GET['idPrograma'];
	 }
	 $idNivelUso = '';
	 if ( !empty($_GET['idNivelUso']) && is_numeric($_GET['idNivelUso']) ){
		 $idNivelUso = 'AND ctl_nivel_usoid = '.$_GET['idNivelUso'];
	 }
	 $idGrupo = '';
	 if ( !empty($_GET['idGrupo']) && is_numeric($_GET['idGrupo']) ){
		 $idGrupo = 'AND grupoid = '.$_GET['idGrupo'];
	 }
	 $idUnidad = '';
	 if ( !empty($_GET['idUnidad']) && is_numeric($_GET['idUnidad']) ){
		 $idUnidad = 'AND ctl_unidad_medidaid = '.$_GET['idUnidad'];
	 }
	 $select = "id, codigo_sinab, codigo_nu, grupoid, nombre_largo_insumo, ctl_nivel_usoid AS nivel_uso, ctl_presentacionid AS presentacion_id, listado_oficial";
	 $sql = "SELECT $select FROM ctl_insumo WHERE enable_schema = 1 $idGrupo $idPrograma $idNivelUso $idUnidad";
	 $array = $app['dbs']['insumo']->fetchAll($sql);
	 return $app->json(array('respuesta' => $array), 201);
 });

 /********************************************************************** Listas generales **************************/
 //Listado de grupos de productos
 $insumo->get('/grupos', function () use ($app) {
	 $tocken = $_GET["tocken"];
	 $acceso = $app['autentica'];
	 if (!$acceso($app, $_GET["tocken"])){ return $app->json($error, 404); }
	 $select = "id, codigo_grupo, nombre_grupo, detalle_grupo, grupo_id, suministro_id";
	 $sql = "SELECT $select FROM ctl_grupo WHERE enable_schema = 1"; 
	 $array = $app['dbs']['insumo']->fetchAll($sql);
	 return $app->json(array('respuesta' => $array), 201);
 });

 //Listado de tipos de suministros
 $insumo->get('/tipos', function () use ($app) {
	 $tocken = $_GET["tocken"];
	 $acceso = $app['autentica'];
	 if (!$acceso($app, $_GET["tocken"])){ return $app->json($error, 404); }
	 $select = "id, codificacion_suministro, nombre_suministro, detalle_suministro, ctl_suministroid AS suministro_id";
	 $sql = "SELECT $select FROM ctl_suministro WHERE enable_schema = 1"; 
	 $array = $app['dbs']['insumo']->fetchAll($sql);
	 return $app->json(array('respuesta' => $array), 201);
 });


 //Listado de programas asociados a insumos
 $insumo->get('/programas', function () use ($app) {
	 $tocken = $_GET["tocken"];
	 $acceso = $app['autentica'];
	 if (!$acceso($app, $_GET["tocken"])){ return $app->json($error, 404); }
	 $select = "id, nombre_programa, detalle_programa, registro_schema, programa_id";
	 $sql = "SELECT $select FROM ctl_programa WHERE enable_schema = 1"; 
	 $array = $app['dbs']['insumo']->fetchAll($sql);
	 return $app->json(array('respuesta' => $array), 201);
 });

 //Listado de niveles de uso asociados a insumos
 $insumo->get('/niveles', function () use ($app) {
	 $tocken = $_GET["tocken"];
	 $acceso = $app['autentica'];
	 if (!$acceso($app, $_GET["tocken"])){ return $app->json($error, 404); }
	 $select = "id, nombre_nivel_uso, detalle_nivel_uso, registro_schema";
	 $sql = "SELECT $select FROM ctl_nivel_uso WHERE enable_schema = 1"; 
	 $array = $app['dbs']['insumo']->fetchAll($sql);
	 return $app->json(array('respuesta' => $array), 201);
 });

 //Listado de principios activos asociados a medicamentos
 $insumo->get('/principios', function () use ($app) {
	 $tocken = $_GET["tocken"];
	 $acceso = $app['autentica'];
	 if (!$acceso($app, $_GET["tocken"])){ return $app->json($error, 404); }
	 $select = "id, nombre_principio, tipo_principio,
	 CASE WHEN tipo_principio=1 THEN 'Natural'
            WHEN tipo_principio=2 THEN 'Sintetico'
            ELSE 'ND'
      END AS detalle,
	  registro_schema";
	 $sql = "SELECT $select FROM ctl_principio WHERE enable_schema = 1"; 
	 $array = $app['dbs']['insumo']->fetchAll($sql);
	 return $app->json(array('respuesta' => $array), 201);
 });

 //Listado de unidades de medida asociadas a medicamentos
 $insumo->get('/unidades', function () use ($app) {
	 $tocken = $_GET["tocken"];
	 $acceso = $app['autentica'];
	 if (!$acceso($app, $_GET["tocken"])){ return $app->json($error, 404); }
	 $select = "id, nombre_unidad, detalle_unidad_medida, unidades_unidad AS unidades_contenidas, ctl_unidad_medidaid AS unidad_id";
	 $sql = "SELECT $select FROM ctl_unidad_medida WHERE enable_schema = 1"; 
	 $array = $app['dbs']['insumo']->fetchAll($sql);
	 return $app->json(array('respuesta' => $array), 201);
 });

//Listado de esquemas de informacion 
 $insumo->get('/esquemas', function () use ($app) {
	 $tocken = $_GET["tocken"];
	 $acceso = $app['autentica'];
	 if (!$acceso($app, $_GET["tocken"])){ return $app->json($error, 404); }
	 $sql = "SELECT schema_name FROM information_schema.schemata WHERE schema_name NOT LIKE 'p%'and schema_name <> 'information_schema'"; 
	 $array = $app['dbs']['insumo']->fetchAll($sql);
	 return $app->json(array('respuesta' => $array), 201);
 });

?>

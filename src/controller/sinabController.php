<?php

 /*
  * Consultas a servicios relacionados al SINAB
  *
  */
 $sinab = $app['controllers_factory'];
 
 //Listado de procesos de compra en el año en curso
 $sinab->get('/procesoscompras', function () use ($app) {
	 $tocken = $_GET["tocken"];
	 
	 $acceso = $app['autentica'];
	 if (!$acceso($app, $_GET["tocken"])){ return $app->json($error, 404); }
	 $anyo = date('Y', strtotime('-1 year'));
	 $select = " NUMEROCONTRATO, IDPROVEEDOR, IDESTABLECIMIENTO, IDCONTRATO, NUMEROMODALIDADCOMPRA,	MONTOCONTRATO ";
	 $sql = "SELECT $select FROM [dbo].[SAB_UACI_CONTRATOS] WHERE [IDTIPODOCUMENTO] = '2' AND  [NUMEROCONTRATO] LIKE '%$anyo' ORDER BY [FECHAGENERACION] DESC";
	 $array_final = array();
	 try {
		 $dbh = mssql_connect("127.0.0.1:1433", 'sa', 'passwd' );
		 if (!$dbh || !mssql_select_db('abastecimiento', $dbh)) {
			 die('algo paso con MSSQL');
		 }
		 else
		 {
			 $query = mssql_query($sql);
			 while ($row = mssql_fetch_array($query)) {
				array_push($array_final, $row );
			 }			 
		 }
	 }
	 catch(PDOException $e) 
	 { return 0; }
	 //echo $app['dbs']['api']['_params']['driver'];
	 
	 return $app->json(array('respuesta' => $array_final), 201);
 });
 
 
 //Listado de establecimientos
 $sinab->get('/establecimientos', function () use ($app) {
	 $tocken = $_GET["tocken"];
	 
	 $acceso = $app['autentica'];
	 if (!$acceso($app, $_GET["tocken"])){ return $app->json($error, 404); }
	 $anyo = date("Y")-1;
	 $select = " IDESTABLECIMIENTO, CODIGOESTABLECIMIENTO, IDMAESTRO, NOMBRE ";
	 $sql = "SELECT $select FROM [dbo].[SAB_CAT_ESTABLECIMIENTOS] ORDER BY [NOMBRE] DESC";
	 $array_final = array();
	 try {
		 $dbh = mssql_connect("127.0.0.1:1433", 'sa', 'passwd' );
		 if (!$dbh || !mssql_select_db('abastecimiento', $dbh)) {
			 die('algo paso con MSSQL');
		 }
		 else
		 {
			 $query = mssql_query($sql);
			 while ($row = mssql_fetch_array($query)) {
				array_push($array_final, $row );
			 }			 
		 }
	 }
	 catch(PDOException $e) 
	 { return 0; }	 
	 return $app->json(array('respuesta' => $array_final), 201);
 });
  
 //Listado de productos
 $sinab->get('/productos', function () use ($app) {
	 $tocken = $_GET["tocken"];
	 
	 $acceso = $app['autentica'];
	 if (!$acceso($app, $_GET["tocken"])){ return $app->json($error, 404); }
	 $anyo = date("Y")-1;
	 $select = " IDPRODUCTO, CORRPRODUCTO, IDUNIDADMEDIDA, DESCRIPCION AS NOMBREUNIDADMEDIDA, DESCLARGO AS NOMBRE ";
	 $sql = "SELECT $select FROM [dbo].[vv_CATALOGOPRODUCTOS] ORDER BY [DESCLARGO] DESC";
	 $array_final = array();
	 try {
		 $dbh = mssql_connect("127.0.0.1:1433", 'sa', 'passwd' );
		 if (!$dbh || !mssql_select_db('abastecimiento', $dbh)) {
			 die('algo paso con MSSQL');
		 }
		 else
		 {
			 $query = mssql_query($sql);
			 while ($row = mssql_fetch_array($query)) {
				array_push($array_final, $row );
			 }			 
		 }
	 }
	 catch(PDOException $e) 
	 { return 0; }	 
	 return $app->json(array('respuesta' => $array_final), 201);
 });
 
  //Listado de almacenes por lotes
 $sinab->get('/almaceneslotes', function () use ($app) {
	 $tocken = $_GET["tocken"];
	 
	 $acceso = $app['autentica'];
	 if (!$acceso($app, $_GET["tocken"])){ return $app->json($error, 404); }
	 $anyo = date("Y")-1;
	 $select = " IDALMACEN, IDPRODUCTO, IDUNIDADMEDIDA, PRECIOLOTE, CODIGO, FECHAVENCIMIENTO, IDLOTE ";
	 $sql = "SELECT $select FROM [dbo].[SAB_ALM_LOTES] ";
	 $array_final = array();
	 try {
		 $dbh = mssql_connect("127.0.0.1:1433", 'sa', 'passwd' );
		 if (!$dbh || !mssql_select_db('abastecimiento', $dbh)) {
			 die('algo paso con MSSQL');
		 }
		 else
		 {
			 $query = mssql_query($sql);
			 while ($row = mssql_fetch_array($query)) {
				array_push($array_final, $row );
			 }			 
		 }
	 }
	 catch(PDOException $e) 
	 { return 0; }	 
	 return $app->json(array('respuesta' => $array_final), 201);
 });
 
  
  //Estimacion de necesidades
 $sinab->get('/estimancionnecesidades', function () use ($app) {
	 $tocken = $_GET["tocken"];
	 $establecimiento = '';
	 $estimacion = '';
	 if ( !empty($_GET['establecimiento']) && is_numeric($_GET['establecimiento']) ){
		 $min = 'AND anio_apertura >= '.$_GET['establecimiento'];
	 }
	 if ( !empty($_GET['estimacion']) && is_numeric($_GET['estimacion']) ){
		 $max = 'AND anio_apertura <= '.$_GET['estimacion'];
	 }
	 
	 $acceso = $app['autentica'];
	 if (!$acceso($app, $_GET["tocken"])){ return $app->json($error, 404); }
	 $anyo = date("Y")-1;
	 $select = " IDALMACEN, IDPRODUCTO, IDUNIDADMEDIDA, PRECIOLOTE, CODIGO, FECHAVENCIMIENTO, IDLOTE ";
	 $sql = " SELECT 
       CP.CORRPRODUCTO, CP.DESCLARGO, CP.DESCRIPCION, CP.CLASIFICACION, CP.CODIGONACIONESUNIDAS,
      convert(decimal(15,4), (PP.PRECIO * (1+(P.INDICEINFLACION/100)))) AS PRECIO, 
       PPE.IDPROGRAMACION, PPE.IDPRODUCTO, PPE.IDESTABLECIMIENTO, PPE.COMPRATRANSITO, 
       PPE.CANTIDADALMACEN, PPE.CANTIDADREGION, PPE.CONSUMOPROMEDIO, PPE.CONSUMOPROMEDIOAJUSTADO, 
       PPE.COBERTURA, CANTIDADCOMPRAR, CANTIDADCOMPRARAJUSTADA, 
      convert(decimal(15,4), ((PP.PRECIO * (1+(P.INDICEINFLACION/100))) * CANTIDADCOMPRARAJUSTADA)) AS MONTOTOTALAJUSTADO, CONSUMOTOTAL,
      convert(decimal(15,4), ((PP.PRECIO * (1+(P.INDICEINFLACION/100))) * CANTIDADCOMPRAR)) AS MONTOTOTAL,
      CASE CP.CLASIFICACION 
          WHEN 'V' THEN 1 
          WHEN 'E' THEN 2 
          WHEN 'N' THEN 3 
          WHEN '1' THEN 4 
          WHEN '2' THEN 5 
          ELSE 6 
        END AS GRUPO, 
        CASE CP.CLASIFICACION 
          WHEN 'V' THEN 'Vital' 
          WHEN 'E' THEN 'Esencial' 
          WHEN 'N' THEN 'No Esencial' 
          WHEN '1' THEN 'Prioridad 1' 
          WHEN '2' THEN 'Prioridad 2' 
          ELSE 'Sin Clasificar' 
        END AS DESCCLASE, 
        (
          SELECT 
            ISNULL(SUM(IDOBSERVACION),0) 
          FROM 
            SAB_URMIM_PROGRAMACIONOBSERVACION 
          WHERE 
            IDPROGRAMACION = PPE.IDPROGRAMACION AND 
            IDESTABLECIMIENTO = PPE.IDESTABLECIMIENTO AND 
            IDPRODUCTO = PPE.IDPRODUCTO AND 
            TIPO = 1
        ) as NOOBSERVACION1,
        (
          SELECT 
            ISNULL(SUM(IDOBSERVACION),0)  
          FROM 
            SAB_URMIM_PROGRAMACIONOBSERVACION 
          WHERE 
            IDPROGRAMACION = PPE.IDPROGRAMACION AND 
            IDESTABLECIMIENTO = PPE.IDESTABLECIMIENTO AND 
            IDPRODUCTO = PPE.IDPRODUCTO AND 
            TIPO = 2
        ) as NOOBSERVACION2
     from 
       SAB_URMIM_PROGRAMACIONPRODUCTOESTABLECIMIENTO PPE 
    	inner join vv_CATALOGOPRODUCTOS CP 
    	  ON PPE.IDPRODUCTO = CP.IDPRODUCTO 
    	inner join SAB_URMIM_PROGRAMACIONPRODUCTO PP 
    	  ON PPE.IDPROGRAMACION = PP.IDPROGRAMACION AND 
    		 PPE.IDPRODUCTO = PP.IDPRODUCTO 
    	inner join SAB_URMIM_PROGRAMACION P 
    	  ON PP.IDPROGRAMACION = P.IDPROGRAMACION 
     WHERE PPE.IDPROGRAMACION = @IDPROGRAMACION AND 
     (PPE.IDESTABLECIMIENTO = @IDESTABLECIMIENTO OR @IDESTABLECIMIENTO = 0) ";
	 $array_final = array();
	 try {
		 $dbh = mssql_connect("127.0.0.1:1433", 'sa', 'passwd' );
		 if (!$dbh || !mssql_select_db('abastecimiento', $dbh)) {
			 die('algo paso con MSSQL');
		 }
		 else
		 {
			 $query = mssql_query($sql);
			 while ($row = mssql_fetch_array($query)) {
				array_push($array_final, $row );
			 }			 
		 }
	 }
	 catch(PDOException $e) 
	 { return 0; }	 
	 return $app->json(array('respuesta' => $array_final), 201);
 });
  
  //Listado de proveedores
 $sinab->get('/proveedoresporcontratos', function () use ($app) {
	 $tocken = $_GET["tocken"];
	 $acceso = $app['autentica'];
	 if (!$acceso($app, $_GET["tocken"])){ return $app->json($error, 404); }
	 $select = " DISTINCT P.IDPROVEEDOR,P.NOMBRE AS nombre ,P.nit AS nit, P.CODIGOPROVEEDOR ";
	 $sql = " SELECT $select from SAB_CAT_PROVEEDORES as P";
	 $array_final = array();
	 try {
		 $dbh = mssql_connect("127.0.0.1:1433", 'sa', 'passwd' );
		 if (!$dbh || !mssql_select_db('abastecimiento', $dbh)) {
			 die('algo paso con MSSQL');
		 }
		 else
		 {
			 $query = mssql_query($sql);
			 while ($row = mssql_fetch_array($query)) {
				array_push($array_final, $row );
			 }			 
		 }
	 }
	 catch(PDOException $e) 
	 { return 0; }	 
	 return $app->json(array('respuesta' => $array_final), 201);
 });
 //Listado de lotes solamente de medicamentos
 $sinab->get('/lotesmedicamentos', function () use ($app) {
	 $tocken = $_GET["tocken"];
	 $acceso = $app['autentica'];
	 $anyo = date("Y") - 1;
	 $anyo = $anyo."/01/01";

	 if (!$acceso($app, $_GET["tocken"])){ return $app->json($error, 404); }
	 $select = "lotes.IDLOTE,lotes.IDUNIDADMEDIDA,lotes.IDPRODUCTO,lotes.CODIGO,lotes.FECHAVENCIMIENTO,lotes.PRECIOLOTE ";
	 $sql = " SELECT $select FROM SAB_ALM_LOTES AS lotes JOIN SAB_CAT_CATALOGOPRODUCTOS As productos ON lotes.IDPRODUCTO=productos.IDPRODUCTO JOIN SAB_CAT_SUBGRUPOS AS sub ON productos.IDTIPOPRODUCTO=sub.IDGRUPO JOIN SAB_CAT_GRUPOS AS g ON sub.IDGRUPO=G.IDGRUPO JOIN SAB_CAT_SUMINISTROS AS s ON g.IDSUMINISTRO=s.IDSUMINISTRO WHERE lotes.AUFECHACREACION >= '2016/01/01' AND s.IDSUMINISTRO=1 ORDER BY lotes.IDLOTE";
	 $array_final = array();
	 try {
		 $dbh = mssql_connect("127.0.0.1:1433", 'sa', 'passwd' );
		 if (!$dbh || !mssql_select_db('abastecimiento', $dbh)) {
			 die('algo paso con MSSQL');
		 }
		 else
		 {
			 $query = mssql_query($sql);
			 while ($row = mssql_fetch_array($query)) {
				array_push($array_final, $row );
			 }			 
		 }
	 }
	 catch(PDOException $e) 
	 { return 0; }	 
	 return $app->json(array('respuesta' => $array_final), 201);
 });

 //Listado de Programaciones o estimacion de necesidades solo de medicamentos
 $sinab->get('/estimacionesmedicamentos', function () use ($app) {
	 $tocken = $_GET["tocken"];
	 $acceso = $app['autentica'];
	 if (!$acceso($app, $_GET["tocken"])){ return $app->json($error, 404); }
	 $select = "IDPROGRAMACION, DESCRIPCION";
	 $sql = "SELECT IDPROGRAMACION, DESCRIPCION
FROM SAB_URMIM_PROGRAMACION
WHERE AUFECHACREACION >= '2016/01/01' AND AUFECHACREACION <= '2016/12/31' AND IDSUMINISTRO = '1'";
	 $array_final = array();
	 try {
		 $dbh = mssql_connect("127.0.0.1:1433", 'sa', 'passwd' );
		 if (!$dbh || !mssql_select_db('abastecimiento', $dbh)) {
			 die('algo paso con MSSQL');
		 }
		 else
		 {
			 $query = mssql_query($sql);
			 while ($row = mssql_fetch_array($query)) {
				array_push($array_final, $row );
			 }			 
		 }
	 }
	 catch(PDOException $e) 
	 { return 0; }	 
	 return $app->json(array('respuesta' => $array_final), 201);
 });

 //Listado de unidades de medidas
 $sinab->get('/unidadesmedidas', function () use ($app) {
	 $tocken = $_GET["tocken"];
	 $acceso = $app['autentica'];
	 if (!$acceso($app, $_GET["tocken"])){ return $app->json($error, 404); }
	 $select = "IDUNIDADMEDIDA, DESCRIPCION";
	 $sql = " SELECT $select FROM SAB_CAT_UNIDADMEDIDAS";
	 $array_final = array();
	 try {
		 $dbh = mssql_connect("127.0.0.1:1433", 'sa', 'passwd' );
		 if (!$dbh || !mssql_select_db('abastecimiento', $dbh)) {
			 die('algo paso con MSSQL');
		 }
		 else
		 {
			 $query = mssql_query($sql);
			 while ($row = mssql_fetch_array($query)) {
				array_push($array_final, $row );
			 }			 
		 }
	 }
	 catch(PDOException $e) 
	 { return 0; }	 
	 return $app->json(array('respuesta' => $array_final), 201);
 });

 //Listado de medicamentos
 $sinab->get('/medicamentos', function () use ($app) {
	 $tocken = $_GET["tocken"];
	 $acceso = $app['autentica'];
	 if (!$acceso($app, $_GET["tocken"])){ return $app->json($error, 404); }
	 $select = "DISTINCT p.IDPRODUCTO, p.CODIGO, p.NOMBRE, p.IDUNIDADMEDIDA, p.CONCENTRACION,p.PRESENTACION";
	 $sql = " SELECT $select FROM SAB_CAT_CATALOGOPRODUCTOS AS p JOIN SAB_CAT_SUBGRUPOS AS sub ON sub.IDGRUPO=p.IDTIPOPRODUCTO JOIN SAB_CAT_GRUPOS AS g ON sub.IDGRUPO=G.IDGRUPO JOIN SAB_CAT_SUMINISTROS AS s ON g.IDSUMINISTRO=s.IDSUMINISTRO ORDER BY p.NOMBRE";
	 $array_final = array();
	 try {
		 $dbh = mssql_connect("127.0.0.1:1433", 'sa', 'passwd' );
		 if (!$dbh || !mssql_select_db('abastecimiento', $dbh)) {
			 die('algo paso con MSSQL');
		 }
		 else
		 {
			 $query = mssql_query($sql);
			 while ($row = mssql_fetch_array($query)) {
				array_push($array_final, $row );
			 }			 
		 }
	 }
	 catch(PDOException $e) 
	 { return 0; }	 
	 return $app->json(array('respuesta' => $array_final), 201);
 });

//Listado de ALMACENES
 $sinab->get('/almacenes', function () use ($app) {
	 $tocken = $_GET["tocken"];
	 $acceso = $app['autentica'];
	 if (!$acceso($app, $_GET["tocken"])){ return $app->json($error, 404); }
	 $select = "IDALMACEN, NOMBRE, DIRECCION";
	 $sql = " SELECT $select FROM SAB_CAT_ALMACENES";
	 $array_final = array();
	 try {
		 $dbh = mssql_connect("127.0.0.1:1433", 'sa', 'passwd' );
		 if (!$dbh || !mssql_select_db('abastecimiento', $dbh)) {
			 die('algo paso con MSSQL');
		 }
		 else
		 {
			 $query = mssql_query($sql);
			 while ($row = mssql_fetch_array($query)) {
				array_push($array_final, $row );
			 }			 
		 }
	 }
	 catch(PDOException $e) 
	 { return 0; }	 
	 return $app->json(array('respuesta' => $array_final), 201);
 });


//Listado de medicamentos en una estimacion por id de programacion
 $sinab->get('/medicamentosestimacion', function () use ($app) {
	 $tocken = $_GET["tocken"];
	 $programacion = '';
	 if ( !empty($_GET['programacion']) && !empty($_GET['contrato'])){
	 	$programacion = "WHERE PPE.IDPROGRAMACION=".$_GET['programacion']." AND pro.IDCONTRATO =".$_GET['contrato'];
	 }
	 $acceso = $app['autentica'];
	 if (!$acceso($app, $_GET["tocken"])){ return $app->json($error, 404); }
	 $select = "DISTINCT PPE.IDPROGRAMACION, PPE.IDPRODUCTO, pro.IDCONTRATO ";
	 $sql = "SELECT $select from 
       SAB_URMIM_PROGRAMACIONPRODUCTOESTABLECIMIENTO PPE 
    	inner join SAB_CAT_CATALOGOPRODUCTOS CP 
    	  ON PPE.IDPRODUCTO = CP.IDPRODUCTO 
    	inner join SAB_URMIM_PROGRAMACIONPRODUCTO PP 
    	  ON PPE.IDPROGRAMACION = PP.IDPROGRAMACION AND 
    		 PPE.IDPRODUCTO = PP.IDPRODUCTO 
    	inner join SAB_URMIM_PROGRAMACION P 
    	  ON PP.IDPROGRAMACION = P.IDPROGRAMACION
JOIN  SAB_UACI_PRODUCTOSCONTRATO as pro on pro.IDPRODUCTO = PPE.IDPRODUCTO $programacion";
	 $array_final = array();
	 try {
		 $dbh = mssql_connect("127.0.0.1:1433", 'sa', 'passwd' );
		 if (!$dbh || !mssql_select_db('abastecimiento', $dbh)) {
			 die('algo paso con MSSQL');
		 }
		 else
		 {
			 $query = mssql_query($sql);
			 while ($row = mssql_fetch_array($query)) {
				array_push($array_final, $row );
			 }			 
		 }
	 }
	 catch(PDOException $e) 
	 { return 0; }	 
	 return $app->json(array('respuesta' => $array_final), 201);
 });

 //Listado de medicamentos por contratos
 $sinab->get('/medicamentoscontratos', function () use ($app) {
	 $tocken = $_GET["tocken"];
	 $anyo = date('Y', strtotime('-1 year'));
	 $acceso = $app['autentica'];
	 if (!$acceso($app, $_GET["tocken"])){ return $app->json($error, 404); }
	 $select = " DISTINCT pc.IDPRODUCTO, pc.IDPROVEEDOR, pc.IDCONTRATO, pc.CANTIDAD, pc.PRECIOUNITARIO ";
	 $sql = " SELECT $select 
     FROM SAB_UACI_PRODUCTOSCONTRATO AS pc
INNER JOIN SAB_UACI_CONTRATOS AS c ON c.IDCONTRATO=pc.IDCONTRATO
WHERE pc.AUFECHACREACION >= '2016/01/01' AND pc.AUFECHACREACION <= '2016/12/31' AND c.IDTIPODOCUMENTO=2
AND c.AUFECHACREACION >= '2016/01/01' AND c.AUFECHACREACION <= '2016/12/31'";
	 $array_final = array();
	 try {
		 $dbh = mssql_connect("127.0.0.1:1433", 'sa', 'passwd' );
		 if (!$dbh || !mssql_select_db('abastecimiento', $dbh)) {
			 die('algo paso con MSSQL');
		 }
		 else
		 {
			 $query = mssql_query($sql);
			 while ($row = mssql_fetch_array($query)) {
				array_push($array_final, $row );
			 }			 
		 }
	 }
	 catch(PDOException $e) 
	 { return 0; }	 
	 return $app->json(array('respuesta' => $array_final), 201);
 });

 //Existencias de productos por establecimiento
 $sinab->get('/existenciaporestablecimiento', function () use ($app) {
	 $tocken = $_GET["tocken"];
	 $acceso = $app['autentica'];
	 if (!$acceso($app, $_GET["tocken"])){ return $app->json($error, 404); }
	 
	 $arr = explode(',', $_GET['ids']);
	 foreach ($arr as $val) {
		 if (!is_numeric($val))
		 return $app->json(array('error' => 'No interpreto bien tu pregunta.'), 404);
	 }
	 $ids = $_GET['ids'];
	 $select = " IDESTABLECIMIENTO AS establecimiento, IDPRODUCTO AS producto, existencia AS existencia ";
	 $sql = " SELECT $select 
       FROM vv_EXISTENCIASESTABLECIMIENTOS
       WHERE IDESTABLECIMIENTO IN ( $ids )";
	 $array_final = array();
	 try {
		 $dbh = mssql_connect("127.0.0.1:1433", 'sa', 'passwd' );
		 if (!$dbh || !mssql_select_db('abastecimiento', $dbh)) {
			 die('algo paso con MSSQL');
		 }
		 else
		 {
			 $query = mssql_query($sql);
			 while ($row = mssql_fetch_array($query)) {
				array_push($array_final, $row );
			 }			 
		 }
	 }
	 catch(PDOException $e) 
	 { return 0; }	 
	 return $app->json(array('respuesta' => $array_final), 201);
 });

//Analisis de cobertura por medicamento a nivel nacional
 $sinab->get('/datoscobertura', function () use ($app) {
	 $tocken = $_GET["tocken"];
	 $producto = $_GET["idproducto"];
	 $programacion = $_GET["programacion"];
	 $acceso = $app['autentica'];
	 if (!$acceso($app, $_GET["tocken"])){ return $app->json($error, 404); }
	 $select = " CP.IDPRODUCTO,CP.DESCLARGO, CP.CLASIFICACION,UM.DESCRIPCION UNIDADMEDIDA,
       PPE.IDPROGRAMACION,SUM(PPE.COMPRATRANSITO) COMPRATRANSITO, SUM(PPE.CONSUMOPROMEDIOAJUSTADO)CONSUMOPROMEDIOAJUSTADO, SUM(E.CANTIDADDISPONIBLE) EXISTENCIAS ";
	 $sql = " SELECT $select 
       FROM  SAB_URMIM_PROGRAMACIONPRODUCTOESTABLECIMIENTO PPE 
      inner join vv_CATALOGOPRODUCTOS CP 
        ON PPE.IDPRODUCTO = CP.IDPRODUCTO 
      inner join SAB_URMIM_PROGRAMACIONPRODUCTO PP 
        ON PPE.IDPROGRAMACION = PP.IDPROGRAMACION AND 
         PPE.IDPRODUCTO = PP.IDPRODUCTO 
      inner join SAB_URMIM_PROGRAMACION P 
        ON PP.IDPROGRAMACION = P.IDPROGRAMACION
      inner join SAB_ALM_EXISTENCIASALMACENES E
        ON E.IDPRODUCTO = CP.IDPRODUCTO
      inner join SAB_CAT_UNIDADMEDIDAS UM
        ON UM.IDUNIDADMEDIDA=CP.IDUNIDADMEDIDA WHERE PPE.IDPROGRAMACION = $programacion
     AND E.CANTIDADDISPONIBLE >= 0
     AND CP.IDPRODUCTO=$producto GROUP BY CP.IDPRODUCTO,CP.CLASIFICACION,PPE.IDPROGRAMACION, PPE.IDPRODUCTO,CP.DESCLARGO,UM.DESCRIPCION";
	 $array_final = array();
	 try {
		 $dbh = mssql_connect("127.0.0.1:1433", 'sa', 'passwd' );
		 if (!$dbh || !mssql_select_db('abastecimiento', $dbh)) {
			 die('algo paso con MSSQL');
		 }
		 else
		 {
			 $query = mssql_query($sql);
			 while ($row = mssql_fetch_array($query)) {
				array_push($array_final, $row );
			 }			 
		 }
	 }
	 catch(PDOException $e) 
	 { return 0; }	 
	 return $app->json(array('respuesta' => $array_final), 201);
 });

 //Existencias de productos por establecimiento filtrado por fecha de caducidad
 $sinab->get('/existenciaporestablecimientoporcaducidad', function () use ($app) {
	 $tocken = $_GET["tocken"];
	 $acceso = $app['autentica'];
	 if (!$acceso($app, $_GET["tocken"])){ return $app->json($error, 404); }
	 
	 $arr = explode(',', $_GET['ids']);
	 foreach ($arr as $val) {
		 if (!is_numeric($val))
		 return $app->json(array('error' => 'No interpreto bien tu pregunta.'), 404);
	 }
	 $fecha = "";
	 if ( !empty($_GET['fecha'])  ){
		 $fecha = "AND (FECHAVENCIMIENTO >= '".$_GET['fecha']."')";
	 }
	 $ids = $_GET['ids'];
	 
	 $select = "  AE.IDESTABLECIMIENTO AS establecimiento, AL.IDPRODUCTO AS producto, SUM(CANTIDADDISPONIBLE) AS existencia ";
	 $sql = " SELECT $select 
       FROM SAB_ALM_LOTES AS AL INNER JOIN SAB_CAT_ALMACENESESTABLECIMIENTOS AE ON AL.IDALMACEN = AE.IDALMACEN
       WHERE  (ESTADISPONIBLE = 1) $fecha AND AE.IDESTABLECIMIENTO IN ( $ids )
       GROUP BY AE.IDESTABLECIMIENTO, AL.IDPRODUCTO ORDER BY AE.IDESTABLECIMIENTO, AL.IDPRODUCTO";
	 $array_final = array();
	 try {
		 $dbh = mssql_connect("127.0.0.1:1433", 'sa', 'passwd' );
		 if (!$dbh || !mssql_select_db('abastecimiento', $dbh)) {
			 die('algo paso con MSSQL');
		 }
		 else
		 {
			 $query = mssql_query($sql);
			 while ($row = mssql_fetch_array($query)) {
				array_push($array_final, $row );
			 }			 
		 }
	 }
	 catch(PDOException $e) 
	 { return 0; }	 
	 return $app->json(array('respuesta' => $array_final), 201);
 });
?>

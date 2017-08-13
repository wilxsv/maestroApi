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
	 $sql = "SELECT DISTINCT C.NUMEROCONTRATO, C.IDPROVEEDOR, C.IDESTABLECIMIENTO, C.IDCONTRATO, C.NUMEROMODALIDADCOMPRA,C.MONTOCONTRATO
FROM SAB_UACI_CONTRATOS AS C INNER JOIN SAB_UACI_PRODUCTOSCONTRATO AS PC 
ON PC.IDCONTRATO=C.IDCONTRATO
WHERE C.IDTIPODOCUMENTO = 1  AND year(C.AUFECHACREACION) > 2016 AND
PC.IDPRODUCTO = ANY (SELECT VV.IDPRODUCTO 
FROM vv_CATALOGOPRODUCTOS VV 
WHERE VV.IDSUMINISTRO IN (1,2,4) 
AND VV.IDPRODUCTO = PC.IDPRODUCTO)";
	 $array_final = array();
	 try {
		 $dbh = mssql_connect("192.168.1.200:1433", 'sa', 'passwd' );
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
 
 
 //Listado de establecimientos ============    2   =================
 $sinab->get('/establecimientos', function () use ($app) {
	 $tocken = $_GET["tocken"];
	 
	 $acceso = $app['autentica'];
	 if (!$acceso($app, $_GET["tocken"])){ return $app->json($error, 404); }
	 $anyo = date("Y")-1;
	 $select = " IDESTABLECIMIENTO, CODIGOESTABLECIMIENTO, IDMAESTRO, NOMBRE ";
	 $sql = "SELECT $select FROM [dbo].[SAB_CAT_ESTABLECIMIENTOS] ORDER BY [NOMBRE] DESC";
	 $array_final = array();
	 try {
		 $dbh = mssql_connect("192.168.1.200:1433", 'sa', 'passwd' );
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
   //Listado de proveedores ============    3   =================
 $sinab->get('/proveedoresporcontratos', function () use ($app) {
	 $tocken = $_GET["tocken"];
	 $acceso = $app['autentica'];
	 if (!$acceso($app, $_GET["tocken"])){ return $app->json($error, 404); }
	 $select = " DISTINCT P.IDPROVEEDOR,P.NOMBRE AS nombre ,P.nit AS nit, P.CODIGOPROVEEDOR ";
	 $sql = " SELECT $select from SAB_CAT_PROVEEDORES as P";
	 $array_final = array();
	 try {
		 $dbh = mssql_connect("192.168.1.200:1433", 'sa', 'passwd' );
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
 //Listado de productos ============ 4 ===============
 $sinab->get('/productos', function () use ($app) {
	 $tocken = $_GET["tocken"];
	 
	 $acceso = $app['autentica'];
	 if (!$acceso($app, $_GET["tocken"])){ return $app->json($error, 404); }
	 $anyo = date("Y")-1;
	 $select = " IDPRODUCTO, CORRPRODUCTO, IDUNIDADMEDIDA, DESCRIPCION AS NOMBREUNIDADMEDIDA, DESCLARGO AS NOMBRE ";
	 $sql = "SELECT $select FROM [dbo].[vv_CATALOGOPRODUCTOS] ORDER BY [DESCLARGO] DESC";
	 $array_final = array();
	 try {
		 $dbh = mssql_connect("192.168.1.200:1433", 'sa', 'passwd' );
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
		 $dbh = mssql_connect("192.168.1.200:1433", 'sa', 'passwd' );
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
	WHERE AUFECHACREACION >= '2015/01/01' AND AUFECHACREACION <= '2015/12/31' AND IDSUMINISTRO = '1'";
	 $array_final = array();
	 try {
		 $dbh = mssql_connect("192.168.1.200:1433", 'sa', 'passwd' );
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

  //Listado de planificaciones de necesidades de los medicamentos
 $sinab->get('/planificacionmedicamentos', function () use ($app) {
	 $tocken = $_GET["tocken"];
	 $acceso = $app['autentica'];
	 if (!$acceso($app, $_GET["tocken"])){ return $app->json($error, 404); }
	 $select = "IDPROGRAMACION, DESCRIPCION";
	 $sql = "SELECT IDPROGRAMACION, DESCRIPCION
	FROM SAB_URMIM_PROGRAMACION
	WHERE AUFECHACREACION >= '2017/01/01' AND AUFECHACREACION <= '2017/12/31' AND IDSUMINISTRO = '1'";
	 $array_final = array();
	 try {
		 $dbh = mssql_connect("192.168.1.200:1433", 'sa', 'passwd' );
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
		 $dbh = mssql_connect("192.168.1.200:1433", 'sa', 'passwd' );
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
	 $select = "p.IDPRODUCTO, p.CORRPRODUCTO, p.DESCPRODUCTO as NOMBRE, p.IDUNIDADMEDIDA, p.IDESTABLECIMIENTO,p.DESCLARGO";
	 $sql = " SELECT $select FROM  vv_CATALOGOPRODUCTOS as p WHERE p.IDSUMINISTRO IN (1)";
	 $array_final = array();
	 try {
		 $dbh = mssql_connect("192.168.1.200:1433", 'sa', 'passwd' );
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
	 $programacion = $_GET["programacion"];
	 $licitacion = $_GET["licitacion"];
	 $proveedor = $_GET["proveedor"];
	 $acceso = $app['autentica'];
	 if (!$acceso($app, $_GET["tocken"])){ return $app->json($error, 404); }
	 $sql = "WITH OBJ(IDPRODUCTO, NUMEROCONTRATO, IDPROVEEDOR) AS (

SELECT DISTINCT PC.IDPRODUCTO,C.NUMEROCONTRATO, PC.IDPROVEEDOR
FROM SAB_UACI_CONTRATOS AS C
INNER JOIN SAB_UACI_PRODUCTOSCONTRATO AS PC 
ON PC.IDCONTRATO=C.IDCONTRATO
AND PC.IDPROVEEDOR = C.IDPROVEEDOR
AND PC.IDESTABLECIMIENTO = C.IDESTABLECIMIENTO

WHERE C.NUMEROMODALIDADCOMPRA = '$licitacion' and C.IDTIPODOCUMENTO=1 AND  C.IDPROVEEDOR = $proveedor

INTERSECT

SELECT DISTINCT PP.IDPRODUCTO,C.NUMEROCONTRATO, C.IDPROVEEDOR
FROM  SAB_URMIM_PROGRAMACIONPRODUCTO PP
INNER JOIN SAB_UACI_PRODUCTOSCONTRATO PC ON PC.IDPRODUCTO = PP.IDPRODUCTO
INNER JOIN SAB_UACI_CONTRATOS C 
ON PC.IDCONTRATO=C.IDCONTRATO
AND PC.IDPROVEEDOR = C.IDPROVEEDOR
AND PC.IDESTABLECIMIENTO = C.IDESTABLECIMIENTO

WHERE PP.IDPROGRAMACION = $programacion )

SELECT OBJ.IDPRODUCTO, OBJ.NUMEROCONTRATO, OBJ.IDPROVEEDOR, PR.NOMBRE, PV.NOMBRE AS PROVEEDOR, PV.NIT FROM OBJ
INNER JOIN SAB_CAT_PROVEEDORES AS PV ON PV.IDPROVEEDOR = OBJ.IDPROVEEDOR 
INNER JOIN SAB_CAT_CATALOGOPRODUCTOS PR ON PR.IDPRODUCTO = OBJ.IDPRODUCTO";
	 $array_final = array();
	
	 try {
		 $dbh = mssql_connect("192.168.1.200:1433", 'sa', 'passwd' );
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
	 $sql = "SELECT DISTINCT PC.IDPRODUCTO, PC.IDPROVEEDOR, PC.IDCONTRATO, PC.CANTIDAD, PC.PRECIOUNITARIO,PC.IDESTABLECIMIENTO,PC.RENGLON
FROM SAB_UACI_CONTRATOS AS C INNER JOIN SAB_UACI_PRODUCTOSCONTRATO AS PC 
ON PC.IDCONTRATO=C.IDCONTRATO
WHERE C.IDTIPODOCUMENTO = 1  AND year(C.AUFECHACREACION) > 2016 AND
PC.IDPRODUCTO = ANY (SELECT VV.IDPRODUCTO 
FROM vv_CATALOGOPRODUCTOS VV 
WHERE VV.IDSUMINISTRO IN (1,2,4) 
AND VV.IDPRODUCTO = PC.IDPRODUCTO)";
	 $array_final = array();
	 try {
		 $dbh = mssql_connect("192.168.1.200:1433", 'sa', 'passwd' );
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
		 $dbh = mssql_connect("192.168.1.200:1433", 'sa', 'passwd' );
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
	 $productos = $_GET["idproductos"];
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
     AND CP.IDPRODUCTO IN ($productos) GROUP BY CP.IDPRODUCTO,CP.CLASIFICACION,PPE.IDPROGRAMACION, PPE.IDPRODUCTO,CP.DESCLARGO,UM.DESCRIPCION";
	 $array_final = array();
	 try {
		 $dbh = mssql_connect("192.168.1.200:1433", 'sa', 'passwd' );
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
		 $dbh = mssql_connect("192.168.1.200:1433", 'sa', 'passwd' );
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

 //existencias de medicamentos no vencidos por todos los establecimientos (sumatoria)
 $sinab->get('/existencianacional', function () use ($app) {
	 $tocken = $_GET["tocken"];
	 $producto = $_GET["productos"];
	 $fecha = $_GET["fecha"];
	 $acceso = $app['autentica'];
	 if (!$acceso($app, $_GET["tocken"])){ return $app->json($error, 404); }
	 $select = "SUM(CANTIDADDISPONIBLE) AS existencia";
	 $sql = " SELECT $select 
     FROM SAB_ALM_LOTES AS AL INNER JOIN SAB_CAT_ALMACENESESTABLECIMIENTOS AE ON AL.IDALMACEN = AE.IDALMACEN
       WHERE  (ESTADISPONIBLE = 1) AND (FECHAVENCIMIENTO >= '$fecha') AND AL.IDPRODUCTO IN($producto)
       GROUP BY AL.IDPRODUCTO ORDER BY AL.IDPRODUCTO";
	 $array_final = array();
	 echo $sql;
	 try {
		 $dbh = mssql_connect("192.168.1.200:1433", 'sa', 'passwd' );
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

//Este recurso se encarga de extraer los datos necesarios para el analizador de prorroga
$sinab->get('/medicamentosplanificacionprorroga', function () use ($app) {
	 $tocken = $_GET["tocken"];
	 //Parametros a revibir
	 $programacion = $_GET["programacion"];
	 $licitacion = $_GET["licitacion"];
	 $establecimiento = $_GET["establecimiento"];
	 $proveedor = $_GET["proveedor"];
	 $acceso = $app['autentica'];
	 if (!$acceso($app, $_GET["tocken"])){ return $app->json($error, 404); }
	 $sql = "SELECT DISTINCT PP.IDPRODUCTO,C.IDCONTRATO, CODIGO , C.NUMEROCONTRATO, C.IDPROVEEDOR, PV.NOMBRE, PC.DESCRIPCIONPROVEEDOR, PPE.IDESTABLECIMIENTO, PPE.CANTIDADALMACEN, PPE.CANTIDADCOMPRAR,PC.PRECIOUNITARIO, ES.NOMBRE 
	 ,dbo.GETPROGRAMA(PPE.IDESTABLECIMIENTO, PP.IDPRODUCTO,659) AS P659
	 ,dbo.GETPROGRAMA(PPE.IDESTABLECIMIENTO, PP.IDPRODUCTO,660) AS P660
	 ,dbo.GETPROGRAMA(PPE.IDESTABLECIMIENTO, PP.IDPRODUCTO,661) AS P661
	 ,dbo.GETPROGRAMA(PPE.IDESTABLECIMIENTO, PP.IDPRODUCTO,663) AS P663
	 ,dbo.GETPROGRAMA(PPE.IDESTABLECIMIENTO, PP.IDPRODUCTO,664) AS P664
	 ,dbo.GETPROGRAMA(PPE.IDESTABLECIMIENTO, PP.IDPRODUCTO,665) AS P665
	 ,dbo.GETPROGRAMA(PPE.IDESTABLECIMIENTO, PP.IDPRODUCTO,666) AS P666

	 FROM  SAB_URMIM_PROGRAMACIONPRODUCTOESTABLECIMIENTO PPE
	 INNER JOIN SAB_URMIM_PROGRAMACIONPRODUCTO PP ON PPE.IDPROGRAMACION = PP.IDPROGRAMACION AND PPE.IDPRODUCTO = PP.IDPRODUCTO 
	 INNER JOIN SAB_URMIM_PROGRAMACION P ON PP.IDPROGRAMACION = P.IDPROGRAMACION 
	 INNER JOIN SAB_UACI_PRODUCTOSCONTRATO PC ON PC.IDPRODUCTO = PP.IDPRODUCTO 
	 INNER JOIN SAB_UACI_CONTRATOS C 
	 ON PC.IDCONTRATO=C.IDCONTRATO
	 AND PC.IDPROVEEDOR = C.IDPROVEEDOR
	 AND PC.IDESTABLECIMIENTO = C.IDESTABLECIMIENTO
	 INNER JOIN SAB_CAT_PROVEEDORES AS PV ON PV.IDPROVEEDOR = C.IDPROVEEDOR 
	 INNER JOIN SAB_CAT_CATALOGOPRODUCTOS PR ON PR.IDPRODUCTO = PP.IDPRODUCTO
	 INNER JOIN SAB_CAT_ESTABLECIMIENTOS ES ON ES.IDESTABLECIMIENTO = PPE.IDESTABLECIMIENTO 

	 WHERE PPE.IDPROGRAMACION = $programacion AND PPE.IDESTABLECIMIENTO = $establecimiento 
	 AND C.NUMEROMODALIDADCOMPRA ='$licitacion' 
	 AND C.IDTIPODOCUMENTO=2 
	 AND C.IDPROVEEDOR =$proveedor
	 AND PC.IDPRODUCTO = ANY (SELECT VV.IDPRODUCTO FROM vv_CATALOGOPRODUCTOS VV WHERE VV.IDSUMINISTRO IN (1,2,4) AND VV.IDPRODUCTO = PC.IDPRODUCTO)";
	 $array_final = array();
	
	 try {
		 $dbh = mssql_connect("192.168.1.200:1433", 'sa', 'passwd' );
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

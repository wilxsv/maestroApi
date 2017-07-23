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
	 $anyo = date("Y")-1;
	 $select = " NUMEROCONTRATO, IDPROVEEDOR, IDESTABLECIMIENTO, IDCONTRATO, NUMEROMODALIDADCOMPRA,	MONTOCONTRATO ";
	 $sql = "SELECT $select FROM [dbo].[SAB_UACI_CONTRATOS] WHERE [IDTIPODOCUMENTO] = '1' AND  [NUMEROCONTRATO] LIKE '%$anyo' ORDER BY [FECHAGENERACION] DESC";
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
  
  //Listado de proveedores por id contrato
 $sinab->get('/proveedoresporcontratos', function () use ($app) {
	 $tocken = $_GET["tocken"];
	 $contrato = '';
	 if ( !empty($_GET['contrato']) ){
		 $contrato = "WHERE C.NUMEROCONTRATO LIKE '".$_GET['contrato']."'";
	 }
	 $acceso = $app['autentica'];
	 if (!$acceso($app, $_GET["tocken"])){ return $app->json($error, 404); }
	 $select = " DISTINCT P.IDPROVEEDOR,P.NOMBRE AS nombre ,P.nit AS nit, P.CODIGOPROVEEDOR ";
	 $sql = " SELECT $select from SAB_CAT_PROVEEDORES as P join SAB_UACI_CONTRATOS as C on C.IDPROVEEDOR=P.IDPROVEEDOR $contrato";
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
	 $anyo = date("Y")-1;
	 if (!$acceso($app, $_GET["tocken"])){ return $app->json($error, 404); }
	 $select = "lotes.IDLOTE,lotes.IDUNIDADMEDIDA,lotes.IDPRODUCTO,lotes.CODIGO,lotes.FECHAVENCIMIENTO,lotes.PRECIOLOTE ";
	 $sql = " SELECT $select FROM SAB_ALM_LOTES AS lotes JOIN SAB_CAT_CATALOGOPRODUCTOS As productos ON lotes.IDPRODUCTO=productos.IDPRODUCTO JOIN SAB_CAT_SUBGRUPOS AS sub ON productos.IDTIPOPRODUCTO=sub.IDGRUPO JOIN SAB_CAT_GRUPOS AS g ON sub.IDGRUPO=G.IDGRUPO JOIN SAB_CAT_SUMINISTROS AS s ON g.IDSUMINISTRO=s.IDSUMINISTRO WHERE lotes.AUFECHACREACION >= '%$anyo' AND s.IDSUMINISTRO=1 ORDER BY lotes.IDLOTE";
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
	 $anyo = date("Y")-1;
	 if (!$acceso($app, $_GET["tocken"])){ return $app->json($error, 404); }
	 $select = "IDPROGRAMACION, DESCRIPCION";
	 $sql = " SELECT $select FROM SAB_URMIM_PROGRAMACION WHERE FECHAPROGRAMACION = '%$anyo' AND IDSUMINISTRO = '1'";
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

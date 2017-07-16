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
	 $select = " NUMEROCONTRATO, NUMEROMODALIDADCOMPRA,	MONTOCONTRATO ";
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
 
?>

<?php

 function autentica( $tocken ){
	 $conn_string = "host=127.0.0.1 port=5432 dbname=maestroapi user=dtic password=dtic";
	 $db = pg_connect($conn_string);
	 $sql = "SELECT * FROM mnt_acceso WHERE tocken_acceso = '$tocken'";
	 
	 $result = pg_query($db, $sql);
	 if (!$result) { return false; }
	 $arr = pg_fetch_array($result, NULL, PGSQL_ASSOC);
	 
	 return $arr["salt"];
 }

 function servicios( ){
	 $conn_string = "host=127.0.0.1 port=5432 dbname=maestroapi user=dtic password=dtic";
	 $db = pg_connect($conn_string);
	 $sql = 'SELECT * FROM "ctl_api"';
	 
	 $result = pg_query($db, $sql);
	 if (!$result) { return false; }
	 $array = pg_fetch_all($result);
	 
	 return $array;//json_encode(new ArrayValue($array), JSON_PRETTY_PRINT);
 }

 function esquemas( $maestro ){
	
	switch ($maestro) {
		case 'establecimiento':
          $conn_string = "host=127.0.0.1 port=5432 dbname=maestroestablecimiento user=dtic password=dtic";
	      $db = pg_connect($conn_string);
		  $sql = "SELECT schema_name FROM information_schema.schemata where schema_name NOT LIKE 'pg%' AND schema_name NOT LIKE 'in%' ";
          $result = pg_query($db, $sql);
          if (!$result) { return false; }
          $array = pg_fetch_all($result);
        break;
		case 'insumo':
          $conn_string = "host=127.0.0.1 port=5432 dbname=maestroinsumo user=dtic password=dtic";
	      $db = pg_connect($conn_string);
		  $sql = "SELECT schema_name FROM information_schema.schemata where schema_name NOT LIKE 'pg%' AND schema_name NOT LIKE 'in%' ";
          $result = pg_query($db, $sql);
          if (!$result) { return false; }
          $array = pg_fetch_all($result);;
        break;
		default:
			return false;
    }

	 return $array;
 }
  
 ?>

	

<?php

 function establecimientosAll( ){
	 $conn_string = "host=127.0.0.1 port=5432 dbname=maestroestablecimiento user=dtic password=dtic";
	 $db = pg_connect($conn_string);
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
	 $result = pg_query($db, $sql);
	 if (!$result) { return false; }
	 $array = pg_fetch_all($result);
	 
	 return $array;
 }
 
 function establecimientos(){
	 return true;
 }

 
 ?>

	

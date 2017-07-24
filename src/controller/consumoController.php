<?php
 
 /*
  * Consultas a servicios relacionados al sistema de consumos y existencias
  *
  */
 $consumo = $app['controllers_factory'];
 
 //ingreso de consumos y existencias
 $consumo->post('/consumosexistencias', function () use ($app) {
	 $tocken = $_GET["tocken"];
	 
	 $acceso = $app['autentica'];
	 $app['autoloader']->registerNamespace('PHPExcel', __DIR__.'/../vendor/PHPExcel/Classes/PHPExcel/IOFactory.php');
	 if (!$acceso($app, $_GET["tocken"])){ return $app->json($error, 404); }

	 		 $nombreArchivo = 'archivo.xls';
			 $objPHPExcel = PHPEXCEL_IOFactory::load($nombreArchivo);
			 $objPHPExcel->setActiveShetIndex(0);
			 $numRows = $objPHPExcel->setActiveShetIndex(0)->getHighestRow();

			 echo '<table border=1><tr><td>Correlativo</td><td>Codigo</td><td>Nombre</td><td>Lote</td><td>Existencia</td><td>Caducidad</td><td>Consumo</td><td>Cubiertos</td><td>Ingresos</td><td>FA</td></tr>';

			 for($i = 1; $i <= $numRows; $i++){
			 	$Correlativo = $objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
			 	$Codigo = $objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();
			 	$Nombre = $objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();
			 	$Lote = $objPHPExcel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue();
			 	$Existencia = $objPHPExcel->getActiveSheet()->getCell('E'.$i)->getCalculatedValue();
			 	$Caducidad = $objPHPExcel->getActiveSheet()->getCell('F'.$i)->getCalculatedValue();
			 	$Consumo = $objPHPExcel->getActiveSheet()->getCell('G'.$i)->getCalculatedValue();
			 	$Cubiertos = $objPHPExcel->getActiveSheet()->getCell('H'.$i)->getCalculatedValue();
			 	$Ingresos = $objPHPExcel->getActiveSheet()->getCell('I'.$i)->getCalculatedValue();
			 	$FA = $objPHPExcel->getActiveSheet()->getCell('J'.$i)->getCalculatedValue();

			 	echo '<tr>';
			 	echo '<td>'.$Correlativo.'</td>';
			 	echo '<td>'.$Codigo.'</td>';
			 	echo '<td>'.$Nombre.'</td>';
			 	echo '<td>'.$Lote.'</td>';
			 	echo '<td>'.$Existencia.'</td>';
			 	echo '<td>'.$Caducidad.'</td>';
			 	echo '<td>'.$Consumo.'</td>';
			 	echo '<td>'.$Cubiertos.'</td>';
			 	echo '<td>'.$Ingresos.'</td>';
			 	echo '<td>'.$FA.'</td>';
			 	echo '</tr>';

			 	$sql = "INSERT INTO ctl_existencias (id, ctl_insumoid, ctl_establecimiento, cantidad_existencia, fecha_caducidad, lote_existencia, fecha_existencia) VALUES('$Correlativo','$Codigo','$Nombre','$Caducidad','$Existencia','$Lote')";
			 }
	 $array = $app['dbs']['establecimiento']->fetchAll($sql);
	 
	 return $app->json(array('respuesta' => $array), 201);
 }); 
 
?>

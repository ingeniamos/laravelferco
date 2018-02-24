<?php
include('config.php');

$connect = mysql_connect($hostname, $username, $password)
or die('Could not connect: ' . mysql_error());
mysql_set_charset('utf8',$connect);
$bool = mysql_select_db($database, $connect);
if ($bool === False){
  print "can't find ".$database."";
}

if (isset($_GET['Movil']))
{
	$VendedorID = $_GET['Movil'];
	$Ord_Compra = "";
	// Fetch Record from Database

	$output = "";
	$output2 = "";
	$sql = mysql_query("SELECT * FROM fact_final WHERE vendedor_codigo = '".$VendedorID."'");
	$columns_total = mysql_num_fields($sql);

	// Get The Field Name

	for ($i = 0; $i < $columns_total; $i++) {
		$heading = mysql_field_name($sql, $i);
		$output .= '"'.$heading.'",';
	}
	$output .="\n";

	// Get Records from the table

	while ($row = mysql_fetch_array($sql)) {
		//for ($i = 0; $i < $columns_total; $i++) {
			//$output .='"'.$row[$i].'",';
			
			$sql2 = mysql_query("SELECT * FROM fact_movs WHERE orden_compra = '".$row['orden_compra']."'");
			$columns_total2 = mysql_num_fields($sql2);
			for ($a = 0; $a < $columns_total2; $a++) {
				$heading2 = mysql_field_name($sql2, $a);
				$output2 .= '"'.$heading2.'";';
			}
			$output2 .="\n";
			while ($row2 = mysql_fetch_array($sql2)) {
				for ($a = 0; $a < $columns_total2; $a++) {
					$output2 .='"'.$row2[$a].'";';
				}
				$output2 .="\n";
			}
			//$sql3 = mysql_query("UPDATE fact_movs SET descargado = 'true' WHERE orden_compra = '".$row['orden_compra']."'");
		//}
		//$output .="\n";
	}
	
	$fp = fopen("../CSV/".$VendedorID.".csv", "w");
	fwrite_stream($fp, $output2);
	fclose($fp);
	echo "Archivo Generado con Exito!";
	// Download the file

	/*$filename = "fact_final.csv";
	header('Content-type: application/csv');
	header('Content-Disposition: attachment; filename='.$filename);
	echo $output;
	$filename2 = "".$VendedorID.".csv";
	header('Content-type: application/csv');
	header('Content-Disposition: attachment; filename='.$filename2);
	echo $output2;*/
}

function fwrite_stream($fp, $string) {
    for ($written = 0; $written < strlen($string); $written += $fwrite) {
        $fwrite = fwrite($fp, substr($string, $written));
        if ($fwrite === false) {
            return $written;
        }
    }
    return $written;
}

//exit;

?>
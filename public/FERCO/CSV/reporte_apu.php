<?php
include('../modulos/config.php');

$connect = mysql_connect($hostname, $username, $password)
or die('Could not connect: ' . mysql_error());
mysql_set_charset('utf8',$connect);
$bool = mysql_select_db($database, $connect);
if ($bool === False){
  print "can't find ".$database."";
}

if (isset($_GET["START"]))
{
	$output = "";
	
	$sql = mysql_query("SELECT codigo, nombre, unidad, valor FROM apu_originales");
	$columns_total = mysql_num_fields($sql);
	
	// Get The Field Name
	
	for ($i = 0; $i < $columns_total; $i++) {
		$heading = mysql_field_name($sql, $i);
		$output .= '"'.$heading.'",';
	}
	$output .="\n";
	
	while ($row = mysql_fetch_array($sql))
	{
		for ($a = 0; $a < $columns_total; $a++)
			$output .='"'.$row[$a].'",';
		$output .="\n";
	}
	
	$fp = fopen("../CSV/Reporte_APU.csv", "w");
	fwrite_stream($fp, $output);
	fclose($fp);
	echo "Archivo Generado con Exito!";
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
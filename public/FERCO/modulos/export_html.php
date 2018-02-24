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
	$done = false;
	// Fetch Record from Database

	$output = "<table><tr>";
	$sql = mysql_query("SELECT * FROM fact_final WHERE vendedor_codigo = '".$VendedorID."'");
	if (mysql_num_rows($sql)<0){
		die();
	}
	// Get Records from the table

	while ($row = mysql_fetch_array($sql))
	{
		$sql2 = mysql_query("SELECT * FROM fact_movs WHERE orden_compra = '".$row['orden_compra']."'");
		$columns_total = mysql_num_fields($sql2);
		if ($done == false) {
			for ($a = 0; $a < $columns_total; $a++) {
				$heading = mysql_field_name($sql2, $a);
				$output .= '<td>'.$heading.'</td>';
			}
			$done = true;
			$output .= '<td>vendedor_id</td>';
			$output .="</tr>";
		}
		while ($row2 = mysql_fetch_array($sql2)) {
			$output .="<tr>";
			for ($a = 0; $a < $columns_total; $a++) {
				$output .='<td>'.$row2[$a].'</td>';
			}
			$output .='<td>'.$VendedorID.'</td>';
			$output .="</tr>";
		}
	}
	$output .= "</table>";
	$fp = fopen("../CSV/".$VendedorID.".html", "w");
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
?>
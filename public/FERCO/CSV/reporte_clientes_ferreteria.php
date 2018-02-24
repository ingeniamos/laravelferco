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
	
	$query = "SELECT * FROM cliente_grupo";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$TipoCliente[$row["cliente_id"]] = $row["tipo"];
		}
	}
	
	//$query = "SELECT * FROM fact_final WHERE estado = 'Aprobado' AND cliente_id != '' GROUP BY cliente_id ORDER BY fecha_remision DESC";
	$query = "SELECT * FROM fact_final WHERE estado = 'Aprobado' AND cliente_id != '' ORDER BY fecha_remision DESC, cliente_id";
	$result = mysql_query($query) or die("SQL ERROR #1: ".mysql_error());
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			if ($TipoCliente[$row["cliente_id"]] == "Ferretería")
			{
				if (!isset($ClientesFecha[$row["cliente_id"]]))
				{
					$ClientesFecha[$row["cliente_id"]] = $row["fecha_remision"];
					$Clientes[] = $row["cliente_id"];
				}
			}
		}
	}
	
	$Clause = implode("', '", $Clientes);
	
	$query = "SELECT cliente_id, SUM(saldo) as saldo_total FROM mov_clientes WHERE saldo > '0' AND cliente_id IN ('".$Clause."') 
	AND tipo_movimiento = 'Debito' AND estado = 'Aprobado' GROUP BY cliente_id";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$DeudaTotal[$row["cliente_id"]] = $row["saldo_total"];
	}
	
	$sql = mysql_query("SELECT * FROM clientes WHERE cliente_id IN ('".$Clause."')");
	$columns_total = mysql_num_fields($sql);
	
	// Get The Field Name

	for ($i = 0; $i < $columns_total; $i++) {
		$heading = mysql_field_name($sql, $i);
		$output .= '"'.$heading.'",';
	}
	$output .="\"Fecha_Facturacion\",";
	$output .="\"Saldo_Cartera\",";
	$output .="\n";
	
	//echo $output;
	//die();
	
	while ($row = mysql_fetch_array($sql))
	{
		for ($a = 0; $a < $columns_total; $a++)
			$output .='"'.$row[$a].'",';
		
		$output .='"'.$ClientesFecha[$row["cliente_id"]].'",';
		if (isset($DeudaTotal[$row["cliente_id"]]))
			$output .='"'.$DeudaTotal[$row["cliente_id"]].'",';
		else
			$output .='"0",';
		$output .="\n";
	}
	
	/*$filename = "Reporte_Clientes_Ferreteria.csv";
	header('Content-type: application/csv');
	header('Content-Disposition: attachment; filename='.$filename);
	echo iconv("UTF-8", "ISO-8859-1", $output);*/
	
	$fp = fopen("../CSV/Reporte_Clientes_Ferreteria.csv", "w");
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
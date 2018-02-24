<?php
$hostname = "localhost";
$username = "root";
$password = "ingeniamos";
$database = "ferco_new";

$connect = mysql_connect($hostname, $username, $password)
or die('Could not connect: ' . mysql_error());
mysql_set_charset('utf8',$connect);
$bool = mysql_select_db($database, $connect);
if ($bool === False){
	print "can't find ".$database."";
}

if (!isset($_GET['START']))
{
	echo "START para Iniciar el Proceso";
	die();
}

// Time Check!
$now = time();
echo "Tiempo de Inicio-> ".$now." <br/><br/>";

// Buscar Despachos "No Finalizado"
$query = "SELECT fact_movs.*, fact_final.* FROM fact_movs LEFT JOIN fact_final ON fact_movs.interno = fact_final.interno 
WHERE cantidad_despachada > '0' AND fact_final.interno != '' AND fact_final.cliente_id != '' AND estado = 'Autorizado'";
$result = mysql_query($query) or die("SQL ERROR #1: ".mysql_error());
if (mysql_num_rows($result) > 0)
{
	while($row = mysql_fetch_array($result))
	{
		// Buscar Productos Despachados "Finalizados"
		$query = "SELECT * FROM fact_movs WHERE cantidad_despachada = '0' AND orden_compra = '".$row["orden_compra"]."' 
		AND codigo = '".$row["codigo"]."'";
		$result1 = mysql_query($query) or die("SQL ERROR #2: ".mysql_error());
		if (mysql_num_rows($result1) > 0)
		{
			$TmpCantidad = 0;
			while ($row1 = mysql_fetch_array($result1))
			{
				$TmpCantidad += $row1["cantidad"];
			}
			
			if ($TmpCantidad != $row["cantidad_despachada"])
			{
				echo "Todo mal con el Interno -> ".$row["interno"]." Orden -> ".$row["orden_compra"]." Fecha -> ".$row["fecha_remision"]."<br />";
				echo "Cantidad -> ".$TmpCantidad." Despachada -> ".$row["cantidad_despachada"]."<br />";
				$query = "UPDATE fact_movs SET 
				cantidad_despachada = '".$TmpCantidad."' 
				WHERE codigo = '".$row["codigo"]."' AND interno = '".$row["interno"]."'";
				$result1 = mysql_query($query) or die("SQL ERROR #3: ".mysql_error());
			}
			else
				echo "Todo bien con el Interno -> ".$row["interno"]." Orden -> ".$row["orden_compra"]."<br />";
		}
	}
}

echo "- Finalizado<br/><br/>";
echo "<br />Tiempo Final -> ".time()." <br/> Tiempo Transcurrido -> ".(time() - $now)." ";
echo "<br/>";

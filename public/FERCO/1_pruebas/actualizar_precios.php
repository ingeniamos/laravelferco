<?php
include('../modulos/config.php');

$connect = mysql_connect($hostname, $username, $password)
or die('Could not connect: ' . mysql_error());
mysql_set_charset('utf8',$connect);
$bool = mysql_select_db($database, $connect);
if ($bool === False){
  print "can't find ".$database."";
}

function CleanNumbers($Number)
{
	if(strstr($Number, ","))
		$Number = str_replace(",", "", $Number);
	return $Number;
}

function CleanNumbers2($Number)
{
	if(strstr($Number, ","))
		$Number = str_replace(",", ".", $Number);
	return $Number;
}

if (isset($_GET["START"]))
{
	include '../PHPExcel-1.8/Classes/PHPExcel/IOFactory.php';
	
	switch ($_GET["START"])
	{
		case "Materiales":
			$File = 'materiales.xls';
			try
			{
				$objPHPExcel = PHPExcel_IOFactory::load($File);
			}
			catch(Exception $e)
			{
				die('Error loading file :' . $e->getMessage());
			}
			
			$XlsxData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
			
			// 0 nothing - 1 real data...
			for ($i = 1; $i < count($XlsxData); $i++)
			{
				$Codigo = $XlsxData[$i]["A"] == NULL ? "":$XlsxData[$i]["A"];
				$Valor = $XlsxData[$i]["E"] == NULL ? 0:CleanNumbers($XlsxData[$i]["E"])+"";
				$Valor = round($Valor);
				
				echo "Codigo ->".$Codigo." || Valor ->".$Valor."<br />";
				
				$query = "UPDATE apu_mat_originales SET valor = '".$Valor."' WHERE codigo = '".$Codigo."'";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
				if (mysql_affected_rows() > 0)
					echo "Success!<br />";
				else
					echo "Failure!<br />";
			}
		break;
		
		case "Equipos":
			$File = 'equipos.xls';
			try
			{
				$objPHPExcel = PHPExcel_IOFactory::load($File);
			}
			catch(Exception $e)
			{
				die('Error loading file :' . $e->getMessage());
			}
			
			$XlsxData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
			
			// 0 nothing - 1 real data...
			for ($i = 1; $i < count($XlsxData); $i++)
			{
				$Codigo = $XlsxData[$i]["A"] == NULL ? "":$XlsxData[$i]["A"];
				$Codigo = str_split($Codigo, 2);
				$Codigo = $Codigo[0].".".$Codigo[1].".".$Codigo[2];
				
				$Valor = $XlsxData[$i]["D"] == NULL ? 0:CleanNumbers($XlsxData[$i]["D"])+"";
				$Valor = round($Valor);
				
				echo "Codigo ->".$Codigo." || Valor ->".$Valor."<br />";
				
				$query = "UPDATE apu_equ_originales SET valor = '".$Valor."' WHERE codigo = '".$Codigo."'";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
				if (mysql_affected_rows() > 0)
					echo "Success!<br />";
				else
					echo "Failure!<br />";
			}
		break;
		
		case "ManodeObra":
			$File = 'mano_de_obra.xls';
			try
			{
				$objPHPExcel = PHPExcel_IOFactory::load($File);
			}
			catch(Exception $e)
			{
				die('Error loading file :' . $e->getMessage());
			}
			
			$XlsxData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
			
			// 0 nothing - 1 real data...
			for ($i = 1; $i < count($XlsxData); $i++)
			{
				$Codigo = $XlsxData[$i]["A"] == NULL ? "":$XlsxData[$i]["A"];
				if (strlen($Codigo) == 5)
				{
					$Codigo = str_split($Codigo);
					$Codigo = "0".$Codigo[0].".".$Codigo[1]."".$Codigo[2].".".$Codigo[3]."".$Codigo[4];
				}
				else
				{
					$Codigo = str_split($Codigo, 2);
					$Codigo = $Codigo[0].".".$Codigo[1].".".$Codigo[2];
				}
				
				$Valor = $XlsxData[$i]["D"] == NULL ? 0:CleanNumbers($XlsxData[$i]["D"])+"";
				$Valor = round($Valor);
				
				echo "Codigo ->".$Codigo." || Valor ->".$Valor."<br />";
				
				$query = "UPDATE apu_mo_originales SET valor_scc = '".$Valor."' WHERE codigo = '".$Codigo."'";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
				if (mysql_affected_rows() > 0)
					echo "Success!<br />";
				else
					echo "Failure!<br />";
			}
		break;
	}
}

?>
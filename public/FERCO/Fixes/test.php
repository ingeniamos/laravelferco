<?php

$String1 = "MANUEL30/12/2015 5:23:51 p. m.";
$String2 = "NOMINA29/04/2014 12:02:55 p.m.";
$String3 = "MF_30/01/2016 09:05:48 a.m.";

echo "String Real -> ";
echo $String3;
echo "<br /><br />";

/*
echo "SUBSTR Real -> ";
//echo substr($String1, -1 -13);
echo substr($String2, -14, 1);
echo "<br /><br />";
if (is_numeric(substr($String1, -13, 1)))
	echo "Es Numerico! <br /><br />";
*/

$Array1 = str_split($String3);

$UserCode = "";
$Day = "";
$Month = "";
$Year = "";
$Hour = "";
$Minute = "";
$Second = "";
$Date = "";
$Type = "";

for ($i = 0; $i < count($Array1); $i++)
{
	if (is_numeric($Array1[$i]))
	{
		if (strlen($Day) < 2)
			$Day .= $Array1[$i];
		else if (strlen($Month) < 2)
			$Month .= $Array1[$i];
		else if (strlen($Year) < 4)
			$Year .= $Array1[$i];
		else if (strlen($Hour) < 1)
		{
			$Hour .= $Array1[$i];
			if ($Array1[$i+1] == ":")
				$Hour = "0".$Hour."";
			else {
				$Hour .= $Array1[$i+1];
				$i++;
			}	
		}
		else if (strlen($Minute) < 2)
			$Minute .= $Array1[$i];
		else if (strlen($Second) < 2)
			$Second .= $Array1[$i];
	}
	else
	{
		if ($Array1[$i] == "_")
			continue;
		
		if ($Array1[$i] == " " || $Array1[$i] == "/" || $Array1[$i] == ":" || $Array1[$i] == ".")
		{
			if (strlen($Type) == 0 && $Array1[$i] == ".")
				$Type .= $Array1[$i-1];
			else if (strlen($Type) == 1 && $Array1[$i] == ".")
				$Type .= $Array1[$i-1];
			
			continue;
		}
		
		if ($Second != "")
		{
			$Date = "".$Year."-".$Month."-".$Day." ".$Hour.":".$Minute.":".$Second."";
			continue;
		}
		
		$UserCode .= $Array1[$i];
	}
}

if (strcasecmp($Type, "AM") == 0)
	$Date = "".$Year."-".$Month."-".$Day." ".$Hour.":".$Minute.":".$Second."";
else
	$Date = "".$Year."-".$Month."-".$Day." ".($Hour == 12 ? $Hour:$Hour + 12).":".$Minute.":".$Second."";

echo "Codigo -> ".$UserCode."";
echo "<br/>";
echo "Fecha -> ".$Date."";
?>
<?php
// Path to dbase file
$db_path = "ICP0315MAT.DBF";

//echo substr_count($db_path, ".");
//echo mb_detect_encoding("Cámara IP tipo bala Antivandálico 1080p - 42Leds                      ", "auto", true);
//echo mb_convert_encoding("C?mara IP tipo bala Antivand?lico 1080p - 42Leds                      ", "UTF-8", "auto");
//echo mb_convert_encoding("Cámara", "UTF-8", "AUTO");
//echo mb_internal_encoding();

echo is_numeric("15")."<br />";

function trim_value(&$value) 
{ 
    $value = trim($value); 
}

$fruit = array(
	'apple' => "512  ",
	'banana ' => "ASD     ", 
	' cranberry ' => "    232asf23 "
);
var_dump($fruit);
echo "<br/>";
array_walk($fruit, 'trim_value');
var_dump($fruit);

die();

// Open dbase file
$dbh = dbase_open($db_path, 0) or die("Error! Could not open dbase database file '$db_path'.");

// Get column information
$column_info = dbase_get_header_info($dbh);
// Get number of columns/fields
$num_fields = dbase_numfields($dbh);
// Get Records
$num_rec = dbase_numrecords($dbh);

// Display information
$Columns = array();

foreach($column_info as $col)
{
	echo "Columna -> ".$col["name"]."<br />";
	$Columns[] = $col["name"];
}

for ($i = 1; $i <= $num_rec; $i++)
{
	$row = @dbase_get_record_with_names($dbh,$i);
	//print_r($row);
	for ($a = 0; $a < $num_fields; $a++)
		echo $Columns[$a]."-> ".$row[$Columns[$a]]." - ID -> ".$i."<br />";
	
}
echo "<br />";
die();

?>
<?php
session_start();
include('config.php');

$connect = mysql_connect($hostname, $username, $password)
or die('Could not connect: ' . mysql_error());
mysql_set_charset('utf8',$connect);

if (isset($_SESSION["UserAccess"]) && $_SESSION["UserAccess"][0]["DB"] != "")
	$bool = mysql_select_db($_SESSION["UserAccess"][0]["DB"], $connect);
else
	$bool = mysql_select_db($database, $connect);

if ($bool === False){
	print "can't find ".$database."";
}


if (isset($_POST["ValidatePass"]))
{
	$UserPW =  $_POST["ValidatePass"];
	$UserPW = mysql_real_escape_string($UserPW);
 
	$query = "SELECT id FROM login WHERE user_pass = '".$UserPW."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		echo "Success";
	}
	else echo "Error";
}
else if (isset($_GET["SetPause"]))
{
	$query = "UPDATE login SET pause = '".$_GET["SetPause"]."' WHERE user_id = '".$_SESSION["UserID"]."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_affected_rows() > 0)
	{
		$_SESSION["UserPause"] = $_GET["SetPause"]; 
		echo "Success";
	}
	else
	{
		$_SESSION["UserPause"] = false; 
		echo "Error";
	}
}
else if (isset($_GET["ShutDown"]))
{
	$Comment = isset($_GET["Comment"]) ? $_GET["Comment"]:"";
	$Status = $_GET["ShutDown"];
	
	$query = "UPDATE login_system SET comments = '".$Comment."', status = '".$Status."' WHERE id = '1'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_affected_rows() > 0)
	{
		echo "Success";
	}
	else
	{
		echo "Error";
	}
}
else if (isset($_GET["GetSystemStatus"]))
{
	$query = "SELECT * FROM login_system WHERE status = 'false'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
		echo "OK";
	else
		echo "ERROR";
}
else if (isset($_GET["CheckChanges"]))
{
	if (!isset($_SESSION["UserID"]))
	{
		echo "LOGIN";
		die();
	}
	
	$query = "SELECT * FROM login_system WHERE status = 'true'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		session_unset();
		session_destroy();
		echo "CLOSED";
		die();
	}
	
	$query = "SELECT user_lvl, active FROM login WHERE user_id = '".$_SESSION["UserID"]."' AND last_update < last_change";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result);
		
		$data[] = array (
			"Lvl" => $row["user_lvl"],
			"Active" => $row["active"],
			"Modulo" => "",
			"SubModulo" => "",
			"Guardar" => "",
			"Modificar" => "",
			"Supervisor" => "",
			"Imprimir" => "",
		);
		
		$query = "SELECT * FROM login_access WHERE user_id = '".$_SESSION["UserID"]."' ";
		$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
		if (mysql_num_rows($result1) > 0)
		{
			while ($row1 = mysql_fetch_array($result1))
			{
				$data[] = array (
					"Modulo" => $row1["modulo"],
					"SubModulo" => $row1["sub_modulo"],
					"Guardar" => $row1["guardar"],
					"Modificar" => $row1["modificar"],
					"Supervisor" => $row1["supervisor"],
					"Imprimir" => $row1["imprimir"],
				);
			}
		}
		
		//print_r($data);
		//die();

		$_SESSION["UserAccess"] = $data;
		
		$query = "UPDATE login SET last_update = last_change WHERE user_id = '".$_SESSION["UserID"]."' ";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #4: ".mysql_error():"");
		
		echo "ACCESS";
	}
	else
	{
		echo "ERROR";
	}
}
else if (isset($_GET["Usuarios"]))
{
	$UserID = isset($_GET["UserID"]) ? $_GET["UserID"]:"";
	
	if ($UserID == "")
		$query = "SELECT nombre, cliente_id FROM clientes";
	else
		$query = "SELECT nombre, cliente_id FROM clientes WHERE cliente_id = '".$UserID."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$ClienteNombre[$row["cliente_id"]] = $row["nombre"];
		}
	}
	
	if ($UserID == "")
		$query = "SELECT * FROM login WHERE id != '1' ORDER BY create_date";
	else
		$query = "SELECT * FROM login WHERE user_id = '".$UserID."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$data[] = array(
				"UserName" => isset($ClienteNombre[$row["user_id"]]) ? $ClienteNombre[$row["user_id"]]:"No Existe.",
				"UserID" => $row["user_id"],
				"UserPW" => $row["user_pass"],
				"UserCode" => $row["user_code"],
				"UserLvl" => $row["user_lvl"],
				"UserActive" => $row["active"],
			);
		}
		echo json_encode($data);
	}
	else
	{
		$data[] = array();
		echo json_encode($data);
	}
}
else if (isset($_GET["Accesos"]))
{
	$query = "SELECT * FROM login_access WHERE user_id = '".$_GET["Accesos"]."' ORDER BY modulo ASC, sub_modulo ASC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$data[] = array(
				"Modulo" => $row["modulo"],
				"SubModulo" => $row["sub_modulo"],
				"Guardar" => $row["guardar"],
				"Modificar" => $row["modificar"],
				"Supervisor" => $row["supervisor"],
				"Imprimir" => $row["imprimir"],
			);
		}
		echo json_encode($data);
	}
}
else if (isset($_GET["Modules"]))
{
	$query = "SELECT DISTINCT module FROM module_access";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$data[] = array(
				"Module" => $row["module"],
			);
		}
		echo json_encode($data);
	}
	else
	{
		$data[] = array();
		echo json_encode($data);
	}
}
else if (isset($_GET["SubModule"]))
{
	$query = "SELECT sub_module FROM module_access WHERE module = '".$_GET["SubModule"]."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$data[] = array(
				"SubModule" => $row["sub_module"],
			);
		}
		echo json_encode($data);
	}
	else
	{
		$data[] = array();
		echo json_encode($data);
	}
}
else if (isset($_GET["Par_Access_Type"]))
{
	$query = "SELECT type FROM module_par_access_type";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$data[] = array(
				"Type" => $row["type"],
			);
		}
		echo json_encode($data);
	}
	else
	{
		$data[] = array();
		echo json_encode($data);
	}
}
else if (isset($_GET["AccessType"]))
{
	$query = "SELECT * FROM module_access_type WHERE tipo = '".$_GET["AccessType"]."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$data[] = array(
				"Modulo" => $row["modulo"],
				"SubModulo" => $row["sub_modulo"],
				"Guardar" => $row["guardar"],
				"Modificar" => $row["modificar"],
				"Supervisor" => $row["supervisor"],
				"Imprimir" => $row["imprimir"],
			);
		}
		echo json_encode($data);
	}
	else
	{
		$data[] = array();
		echo json_encode($data);
	}
}
else if (isset($_GET["Agregar_Usuarios"]))
{
	$UserID = isset($_GET["UserID"]) ? $_GET["UserID"]:die();
	$UserPW = isset($_GET["UserPW"]) ? $_GET["UserPW"]:die();
	$UserCode = isset($_GET["UserCode"]) ? $_GET["UserCode"]:die();
	$UserLvl = isset($_GET["UserLvl"]) ? $_GET["UserLvl"]:die();
	
	$query = "INSERT INTO login 
	(user_id, user_pass, user_code, user_lvl, create_date) VALUES 
	('".$UserID."', '".$UserPW."', '".$UserCode."', '".$UserLvl."', NOW())";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Guardar_Usuarios"]))
{
	$UserID = isset($_GET["UserID"]) ? $_GET["UserID"]:die();
	$UserPW = isset($_GET["UserPW"]) ? $_GET["UserPW"]:die();
	$UserCode = isset($_GET["UserCode"]) ? $_GET["UserCode"]:die();
	$UserCode = strtoupper($UserCode);
	$UserLvl = isset($_GET["UserLvl"]) ? $_GET["UserLvl"]:die();
	$UserActive = isset($_GET["UserActive"]) ? $_GET["UserActive"]:die();
	
	$query = "SELECT * FROM login WHERE user_id = '".$UserID."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		$query = "UPDATE login SET 
		user_pass = '".$UserPW."', 
		user_code = '".$UserCode."', 
		user_lvl = '".$UserLvl."', 
		active = '".$UserActive."', 
		last_change = NOW() 
		WHERE user_id = '".$UserID."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
	}
}
else if (isset($_GET["Borrar_Usuarios"]))
{
	$UserID = isset($_GET["UserID"]) ? $_GET["UserID"]:die();
	
	$query = "DELETE FROM login WHERE user_id = '".$UserID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	$query = "DELETE FROM login_access WHERE user_id = '".$UserID."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
}
else if (isset($_GET["Agregar_Accesos"]))
{
	$UserID = isset($_GET["UserID"]) ? $_GET["UserID"]:die();
	$Modulo = isset($_GET["Modulo"]) ? $_GET["Modulo"]:die();
	$SubModulo = isset($_GET["SubModulo"]) ? $_GET["SubModulo"]:die();
	$Guardar = isset($_GET["Guardar"]) ? $_GET["Guardar"]:die();
	$Modificar = isset($_GET["Modificar"]) ? $_GET["Modificar"]:die();
	$Supervisor = isset($_GET["Supervisor"]) ? $_GET["Supervisor"]:die();
	$Imprimir = isset($_GET["Imprimir"]) ? $_GET["Imprimir"]:die();
	
	$query = "INSERT INTO login_access 
	(user_id, modulo, sub_modulo, guardar, modificar, supervisor, imprimir) VALUES 
	('".$UserID."', '".$Modulo."', '".$SubModulo."', '".$Guardar."', '".$Modificar."', '".$Supervisor."', '".$Imprimir."')";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	$query = "UPDATE login SET last_change = NOW() WHERE user_id = '".$UserID."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
}
else if (isset($_GET["Guardar_Accesos"]))
{
	$UserID = isset($_GET["UserID"]) ? $_GET["UserID"]:die();
	$Modulo = isset($_GET["Modulo"]) ? $_GET["Modulo"]:die();
	$SubModulo = isset($_GET["SubModulo"]) ? $_GET["SubModulo"]:die();
	$Guardar = isset($_GET["Guardar"]) ? $_GET["Guardar"]:die();
	$Modificar = isset($_GET["Modificar"]) ? $_GET["Modificar"]:die();
	$Supervisor = isset($_GET["Supervisor"]) ? $_GET["Supervisor"]:die();
	$Imprimir = isset($_GET["Imprimir"]) ? $_GET["Imprimir"]:die();
	
	$query = "SELECT * FROM login_access WHERE user_id = '".$UserID."' AND modulo = '".$Modulo."' AND sub_modulo = '".$SubModulo."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		$query = "UPDATE login_access SET  
		guardar = '".$Guardar."', 
		modificar = '".$Modificar."', 
		supervisor = '".$Supervisor."',
		imprimir = '".$Imprimir."' 
		WHERE user_id = '".$UserID."' AND modulo = '".$Modulo."' AND sub_modulo = '".$SubModulo."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
	}
	
	$query = "UPDATE login SET last_change = NOW() WHERE user_id = '".$UserID."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
}
else if (isset($_GET["Borrar_Accesos"]))
{
	$UserID = isset($_GET["UserID"]) ? $_GET["UserID"]:die();
	$Modulo = isset($_GET["Modulo"]) ? $_GET["Modulo"]:die();
	$SubModulo = isset($_GET["SubModulo"]) ? $_GET["SubModulo"]:die();
	
	$query = "DELETE FROM login_access WHERE user_id = '".$UserID."' AND modulo = '".$Modulo."' AND sub_modulo = '".$SubModulo."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	$query = "UPDATE login SET last_change = NOW() WHERE user_id = '".$UserID."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
}
else if (isset($_GET["Borrar_Accesos_Todo"]))
{
	$UserID = isset($_GET["UserID"]) ? $_GET["UserID"]:die();
	
	$query = "DELETE FROM login_access WHERE user_id = '".$UserID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	$query = "UPDATE login SET last_change = NOW() WHERE user_id = '".$UserID."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_POST["Registro"]))
{
	//echo "Success";
	//die();
	
	$UserID = $_POST["UserID"];
	$UserID = htmlentities($UserID, ENT_QUOTES, "UTF-8");
	$UserID = mysql_real_escape_string($UserID);
	$UserCode =  $_POST["UserCode"];
	$UserCode = htmlentities($UserCode, ENT_QUOTES, "UTF-8");
	$UserCode = mysql_real_escape_string($UserCode);
	$UserCode = strtoupper($UserCode);
	$UserPass =  $_POST["UserPass"];
	$UserPass = htmlentities($UserPass, ENT_QUOTES, "UTF-8");
	$UserPass = mysql_real_escape_string($UserPass);
	$UserEmail =  $_POST["UserEmail"];
	$UserEmail = htmlentities($UserEmail, ENT_QUOTES, "UTF-8");
	$UserEmail = mysql_real_escape_string($UserEmail);
	
	$query = "SELECT * FROM max_users";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #0: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result);
		
		if ($row["number"] == 50)
		{
			$ReturnData[0] = array(
				"MESSAGE" => "ERROR 1",
			);
		}
		else
		{
			$query = "UPDATE max_users SET number = number + 1 WHERE id = '1'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #0-1: ".mysql_error():"");
			
			$query = "INSERT INTO login (user_id, user_pass, user_code, user_lvl, create_date, email, db) 
			VALUES ('".$UserID."', '".$UserPass."', '".$UserCode."', 'General', NOW(), '".$UserEmail."', 'ingeniam_".$row["number"]."')";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #0-1: ".mysql_error():"");
			
			$query = "INSERT INTO login_access 
			(user_id, modulo, sub_modulo, guardar, modificar, supervisor, imprimir) VALUES 
			('".$UserID."', 'Presupuesto', 'Presupuesto', 'true', 'true', 'false', 'true')";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #0-2: ".mysql_error():"");
			
			$query = "INSERT INTO login_access 
			(user_id, modulo, sub_modulo, guardar, modificar, supervisor, imprimir) VALUES 
			('".$UserID."', 'Presupuesto', 'APU', 'true', 'true', 'false', 'true')";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #0-3: ".mysql_error():"");
			
			$query = "INSERT INTO login_access 
			(user_id, modulo, sub_modulo, guardar, modificar, supervisor, imprimir) VALUES 
			('".$UserID."', 'Presupuesto', 'Mano_de_Obra', 'true', 'false', 'true', 'true')";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #0-4: ".mysql_error():"");
			
			$query = "INSERT INTO login_access 
			(user_id, modulo, sub_modulo, guardar, modificar, supervisor, imprimir) VALUES 
			('".$UserID."', 'Presupuesto', 'Equipos', 'true', 'true', 'false', 'true')";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #0-5: ".mysql_error():"");
			
			$query = "INSERT INTO login_access 
			(user_id, modulo, sub_modulo, guardar, modificar, supervisor, imprimir) VALUES 
			('".$UserID."', 'Presupuesto', 'Materiales', 'true', 'true', 'false', 'true')";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #0-6: ".mysql_error():"");
			
			$query = "INSERT INTO login_access 
			(user_id, modulo, sub_modulo, guardar, modificar, supervisor, imprimir) VALUES 
			('".$UserID."', 'Presupuesto', 'Importar', 'true', 'true', 'false', 'true')";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #0-7: ".mysql_error():"");
			
			if (mysql_select_db("ingeniam_".$row["number"], $connect))
			{
				// ---------------------------------------------------------------------------------------- //
				// ------------------------------------- LOGIN SYSTEM ------------------------------------- //
				// ---------------------------------------------------------------------------------------- //
				// Login
				$query = "CREATE TABLE login LIKE ".$database.".login";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-1: ".mysql_error():"");
				//$query = "INSERT INTO login SELECT * FROM ".$database.".login";
				//$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-2: ".mysql_error():"");
				// Login_Access
				$query = "CREATE TABLE login_access LIKE ".$database.".login_access";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-3: ".mysql_error():"");
				//$query = "INSERT INTO login_access SELECT * FROM ".$database.".login_access";
				//$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-4: ".mysql_error():"");
				// Login_Log
				$query = "CREATE TABLE login_log LIKE ".$database.".login_log";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-5: ".mysql_error():"");
				//$query = "INSERT INTO login_log SELECT * FROM ".$database.".login_log";
				//$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-6: ".mysql_error():"");
				// Login_System
				$query = "CREATE TABLE login_system LIKE ".$database.".login_system";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-7: ".mysql_error():"");
				$query = "INSERT INTO login_system SELECT * FROM ".$database.".login_system";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-8: ".mysql_error():"");
				// Module_Access
				$query = "CREATE TABLE module_access LIKE ".$database.".module_access";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-9: ".mysql_error():"");
				$query = "INSERT INTO module_access SELECT * FROM ".$database.".module_access";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-10: ".mysql_error():"");
				// Module_Access_Type
				$query = "CREATE TABLE module_access_type LIKE ".$database.".module_access_type";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-11: ".mysql_error():"");
				$query = "INSERT INTO module_access_type SELECT * FROM ".$database.".module_access_type";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-12: ".mysql_error():"");
				// Module_Par_Access_Type
				$query = "CREATE TABLE module_par_access_type LIKE ".$database.".module_par_access_type";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-11: ".mysql_error():"");
				$query = "INSERT INTO module_par_access_type SELECT * FROM ".$database.".module_par_access_type";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-12: ".mysql_error():"");
				
				// ---------------------------------------------------------------------------------------- //
				// --------------------------------------- TERCEROS --------------------------------------- //
				// ---------------------------------------------------------------------------------------- //
				// Clientes
				$query = "CREATE TABLE clientes LIKE ".$database.".clientes";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-13: ".mysql_error():"");
				$query = "INSERT INTO clientes SELECT * FROM ".$database.".clientes";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-14: ".mysql_error():"");
				
				// ---------------------------------------------------------------------------------------- //
				// ------------------------------------- PRESUPUESTOS ------------------------------------- //
				// ---------------------------------------------------------------------------------------- //
				$query = "DROP TABLE IF EXISTS `apu_originales`";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3-1: ".mysql_error():"");
				
				$query = "CREATE TABLE `apu_originales` ( ";
				$query .= "`id` int(11) NOT NULL AUTO_INCREMENT, ";
				$query .= "`codigo` varchar(100) NOT NULL DEFAULT '', ";
				$query .= "`codigo2` varchar(100) NOT NULL DEFAULT '', ";
				$query .= "`nombre` varchar(100) NOT NULL DEFAULT '', ";
				$query .= "`categoria` varchar(100) NOT NULL DEFAULT '', ";
				$query .= "`grupo` varchar(100) NOT NULL DEFAULT '', ";
				$query .= "`subgrupo` varchar(100) NOT NULL DEFAULT '', ";
				$query .= "`unidad` varchar(100) NOT NULL DEFAULT '', ";
				$query .= "`desperdicios` decimal(20,2) NOT NULL DEFAULT '0.00', ";
				$query .= "`gastos` decimal(20,2) NOT NULL DEFAULT '0.00', ";
				$query .= "`valor` decimal(20,2) NOT NULL DEFAULT '0.00', ";
				$query .= "`digitado_por` varchar(100) NOT NULL DEFAULT '', ";
				$query .= "`fecha_digitado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00', ";
				$query .= "`modificado_por` varchar(100) NOT NULL DEFAULT '', ";
				$query .= "`fecha_modificado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00', ";
				$query .= "`notas` varchar(100) NOT NULL DEFAULT '', ";
				$query .= "PRIMARY KEY (`id`), ";
				$query .= "KEY `apu_originales_codigo` (`codigo`) ";
				$query .= ") ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1; ";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3-2: ".mysql_error():"");
				
				$query = "DROP TRIGGER IF EXISTS `update_presupuesto_A-U_apu_originales` ";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3-3: ".mysql_error().$query:"");
				
				$query = "CREATE TRIGGER `update_presupuesto_A-U_apu_originales` ";
				$query .= "AFTER UPDATE ON `apu_originales` ";
				$query .= "FOR EACH ROW ";
				$query .= "BEGIN";
				$query .= "	IF NEW.codigo != OLD.codigo THEN ";
				$query .= "		UPDATE presupuesto_movs SET codigo = NEW.codigo WHERE codigo = OLD.codigo; ";
				$query .= "	END IF; ";
				$query .= "END";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3-4: ".mysql_error().$query:"");
				
				$query = "DROP TABLE IF EXISTS `apu_originales_movs`";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3-5: ".mysql_error():"");
				
				$query = "CREATE TABLE `apu_originales_movs` ( ";
				$query .= "`id` int(11) NOT NULL AUTO_INCREMENT, ";
				$query .= "`codigo` varchar(100) NOT NULL DEFAULT '', ";
				$query .= "`objeto_codigo` varchar(100) NOT NULL DEFAULT '', ";
				$query .= "`cantidad` decimal(20,3) NOT NULL DEFAULT '0.000', ";
				$query .= "`uso` decimal(20,2) NOT NULL DEFAULT '0.00', ";
				$query .= "`valor` decimal(20,2) NOT NULL DEFAULT '0.00', ";
				$query .= "`total` decimal(20,2) NOT NULL DEFAULT '0.00', ";
				$query .= "`tipo` varchar(100) NOT NULL DEFAULT '', ";
				$query .= "`fijo` enum('true','false') NOT NULL DEFAULT 'false', ";
				$query .= "`clasificacion` varchar(100) NOT NULL DEFAULT '', ";
				$query .= "PRIMARY KEY (`id`), ";
				$query .= "KEY `apu_originales_codigo` (`codigo`), ";
				$query .= "CONSTRAINT `apu_originales_codigo` FOREIGN KEY (`codigo`) REFERENCES `apu_originales` (`codigo`) ON DELETE CASCADE ON UPDATE CASCADE ";
				$query .= ") ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1; ";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3-6: ".mysql_error():"");
				
				$query = "DROP TRIGGER IF EXISTS `update_apu_A-U_apu_originales_movs` ";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3-7: ".mysql_error().$query:"");
				
				$query = "CREATE TRIGGER `update_apu_A-U_apu_originales_movs` ";
				$query .= "AFTER UPDATE ON `apu_originales_movs` ";
				$query .= "FOR EACH ROW ";
				$query .= "BEGIN";
				$query .= "	DECLARE p_desp decimal(20, 2); ";
				$query .= "	DECLARE total_desp decimal(20, 2); ";
				$query .= "	DECLARE p_gast decimal(20, 2); ";
				$query .= "	DECLARE total_gast decimal(20, 2); ";
				$query .= "	DECLARE total_apu decimal(20, 2); ";
				$query .= "	DECLARE total_mat decimal(20, 2); ";
				$query .= "	DECLARE total_mo decimal(20, 2); ";
				$query .= "	DECLARE total_equ decimal(20, 2); ";
				$query .= "	DECLARE total_final decimal(20, 2); ";
				$query .= "	SELECT desperdicios INTO p_desp FROM `apu_originales` WHERE codigo = NEW.codigo; ";
				$query .= "	SELECT gastos INTO p_gast FROM `apu_originales` WHERE codigo = NEW.codigo; ";
				$query .= "	SELECT SUM(total) INTO total_apu FROM `apu_originales_movs` WHERE tipo = 'APU' AND codigo = NEW.codigo; ";
				$query .= "	SELECT SUM(total) INTO total_mat FROM `apu_originales_movs` WHERE tipo = 'Materiales' AND codigo = NEW.codigo; ";
				$query .= "	SELECT SUM(total) INTO total_mo FROM `apu_originales_movs` WHERE tipo = 'Mano de Obra' AND codigo = NEW.codigo; ";
				$query .= "	SELECT SUM(total) INTO total_equ FROM `apu_originales_movs` WHERE tipo = 'Equipos' AND codigo = NEW.codigo; ";
				$query .= "	SET total_desp := ((total_mat / 100) * p_desp); ";
				$query .= "	SET total_gast := ((total_mo / 100) * p_gast); ";
				$query .= "	IF ISNULL(total_desp) THEN SET total_desp := 0; END IF; ";
				$query .= "	IF ISNULL(total_gast) THEN SET total_gast := 0; END IF; ";
				$query .= "	IF ISNULL(total_apu) THEN SET total_apu := 0; END IF; ";
				$query .= "	IF ISNULL(total_mat) THEN SET total_mat := 0; END IF; ";
				$query .= "	IF ISNULL(total_mo) THEN SET total_mo := 0; END IF; ";
				$query .= "	IF ISNULL(total_equ) THEN SET total_equ := 0; END IF; ";
				$query .= "	SET total_final := total_desp + total_gast + total_apu + total_mat + total_mo + total_equ; ";
				$query .= "	UPDATE `apu_originales` SET valor = total_final WHERE codigo = NEW.codigo; ";
				$query .= "END";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3-8: ".mysql_error():"");
				
				$query = "DROP TABLE IF EXISTS `apu_mo_originales`";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #4-1: ".mysql_error():"");
				
				$query = "CREATE TABLE `apu_mo_originales` ( ";
				$query .= "`id` int(11) NOT NULL AUTO_INCREMENT, ";
				$query .= "`codigo` varchar(100) NOT NULL DEFAULT '', ";
				$query .= "`codigo2` varchar(100) NOT NULL DEFAULT '', ";
				$query .= "`nombre` varchar(100) NOT NULL DEFAULT '', ";
				$query .= "`categoria` varchar(100) NOT NULL DEFAULT '', ";
				$query .= "`grupo` varchar(100) NOT NULL DEFAULT '', ";
				$query .= "`subgrupo` varchar(100) NOT NULL DEFAULT '', ";
				$query .= "`unidad` varchar(100) NOT NULL DEFAULT '', ";
				$query .= "`uso` decimal(20,2) NOT NULL DEFAULT '0.00', ";
				$query .= "`valor_sc` decimal(20,2) NOT NULL DEFAULT '0.00', ";
				$query .= "`valor_pd` decimal(20,2) NOT NULL DEFAULT '0.00', ";
				$query .= "`valor_scc` decimal(20,2) NOT NULL DEFAULT '0.00', ";
				$query .= "`notas` varchar(100) NOT NULL DEFAULT '', ";
				$query .= "`fecha` date NOT NULL DEFAULT '0000-00-00', ";
				$query .= "`digitado_por` varchar(100) NOT NULL DEFAULT '', ";
				$query .= "`fecha_digitado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00', ";
				$query .= "`modificado_por` varchar(100) NOT NULL DEFAULT '', ";
				$query .= "`fecha_modificado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00', ";
				$query .= "PRIMARY KEY (`id`), ";
				$query .= "KEY `apu_mo_originales_codigo` (`codigo`) ";
				$query .= ") ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1; ";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #4-2: ".mysql_error():"");
				
				$query = "DROP TRIGGER IF EXISTS `update_apu_movs_A-U_apu_mo_originales` ";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #4-3: ".mysql_error().$query:"");
				
				$query = "CREATE TRIGGER `update_apu_movs_A-U_apu_mo_originales` ";
				$query .= "AFTER UPDATE ON `apu_mo_originales` ";
				$query .= "FOR EACH ROW ";
				$query .= "BEGIN";
				$query .= "	UPDATE `apu_originales_movs` SET objeto_codigo = NEW.codigo, valor = NEW.valor_scc, total = (((NEW.valor_scc * cantidad) /100) * uso) WHERE tipo = 'Mano de Obra' AND objeto_codigo = OLD.codigo; ";
				$query .= "END";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #4-4: ".mysql_error():"");
				
				$query = "DROP TABLE IF EXISTS `apu_mo_originales_movs`";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #4-5: ".mysql_error():"");
				
				$query = "CREATE TABLE `apu_mo_originales_movs` ( ";
				$query .= "`id` int(11) NOT NULL AUTO_INCREMENT, ";
				$query .= "`codigo` varchar(100) NOT NULL DEFAULT '', ";
				$query .= "`concepto` varchar(100) NOT NULL DEFAULT '', ";
				$query .= "`uso` decimal(20,2) NOT NULL DEFAULT '0.00', ";
				$query .= "`valor` decimal(20,2) NOT NULL DEFAULT '0.00', ";
				$query .= "`total` decimal(20,2) NOT NULL DEFAULT '0.00', ";
				$query .= "`tipo` varchar(100) NOT NULL DEFAULT '', ";
				$query .= "`fijo` enum('true','false') NOT NULL DEFAULT 'false', ";
				$query .= "PRIMARY KEY (`id`), ";
				$query .= "KEY `apu_mo_originales_codigo` (`codigo`), ";
				$query .= "CONSTRAINT `apu_mo_originales_codigo` FOREIGN KEY (`codigo`) REFERENCES `apu_mo_originales` (`codigo`) ON DELETE CASCADE ON UPDATE CASCADE ";
				$query .= ") ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1; ";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #4-6: ".mysql_error():"");
				
				$query = "DROP TABLE IF EXISTS `apu_equ_originales`";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #5-1: ".mysql_error():"");
				
				$query = "CREATE TABLE `apu_equ_originales` ( ";
				$query .= "`id` int(11) NOT NULL AUTO_INCREMENT, ";
				$query .= "`codigo` varchar(100) NOT NULL DEFAULT '', ";
				$query .= "`codigo2` varchar(100) NOT NULL DEFAULT '', ";
				$query .= "`nombre` varchar(100) NOT NULL DEFAULT '', ";
				$query .= "`categoria` varchar(100) NOT NULL DEFAULT '', ";
				$query .= "`grupo` varchar(100) NOT NULL DEFAULT '', ";
				$query .= "`subgrupo` varchar(100) NOT NULL DEFAULT '', ";
				$query .= "`unidad` varchar(100) NOT NULL DEFAULT '', ";
				$query .= "`valor` decimal(20,2) NOT NULL DEFAULT '0.00', ";
				$query .= "`uso` decimal(20,2) NOT NULL DEFAULT '0.00', ";
				$query .= "`notas` varchar(100) NOT NULL DEFAULT '', ";
				$query .= "`fecha` date NOT NULL DEFAULT '0000-00-00', ";
				$query .= "`imagen` varchar(100) NOT NULL DEFAULT '', ";
				$query .= "`digitado_por` varchar(100) NOT NULL DEFAULT '', ";
				$query .= "`fecha_digitado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00', ";
				$query .= "`modificado_por` varchar(100) NOT NULL DEFAULT '', ";
				$query .= "`fecha_modificado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00', ";
				$query .= "PRIMARY KEY (`id`), ";
				$query .= "KEY `apu_equ_originales_codigo` (`codigo`) ";
				$query .= ") ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1; ";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #5-2: ".mysql_error():"");
				
				$query = "DROP TRIGGER IF EXISTS `update_apu_movs_A-U_apu_equ_originales` ";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #5-3: ".mysql_error().$query:"");
				
				$query = "CREATE TRIGGER `update_apu_movs_A-U_apu_equ_originales` ";
				$query .= "AFTER UPDATE ON `apu_equ_originales` ";
				$query .= "FOR EACH ROW ";
				$query .= "BEGIN";
				$query .= "	UPDATE `apu_originales_movs` SET objeto_codigo = NEW.codigo, valor = NEW.valor, total = (((NEW.valor * cantidad) /100) * uso) WHERE tipo = 'Equipos' AND objeto_codigo = OLD.codigo; ";
				$query .= "END";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #5-4: ".mysql_error():"");
				
				$query = "DROP TABLE IF EXISTS `apu_equ_originales_prov`";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #5-5: ".mysql_error():"");
				
				$query = "CREATE TABLE `apu_equ_originales_prov` ( ";
				$query .= "`id` int(11) NOT NULL AUTO_INCREMENT, ";
				$query .= "`codigo` varchar(100) NOT NULL DEFAULT '', ";
				$query .= "`proveedor_id` varchar(100) NOT NULL DEFAULT '', ";
				$query .= "`proveedor_codigo` varchar(100) NOT NULL DEFAULT '', ";
				$query .= "PRIMARY KEY (`id`), ";
				$query .= "KEY `apu_equ_originales_codigo` (`codigo`), ";
				$query .= "CONSTRAINT `apu_equ_originales_codigo` FOREIGN KEY (`codigo`) REFERENCES `apu_equ_originales` (`codigo`) ON DELETE CASCADE ON UPDATE CASCADE ";
				$query .= ") ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1; ";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #5-6: ".mysql_error():"");
				
				$query = "DROP TABLE IF EXISTS `apu_mat_originales`";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #6-1: ".mysql_error():"");
				
				$query = "CREATE TABLE `apu_mat_originales` ( ";
				$query .= "`id` int(11) NOT NULL AUTO_INCREMENT, ";
				$query .= "`codigo` varchar(100) NOT NULL DEFAULT '', ";
				$query .= "`codigo2` varchar(100) NOT NULL DEFAULT '', ";
				$query .= "`nombre` varchar(100) NOT NULL DEFAULT '', ";
				$query .= "`categoria` varchar(100) NOT NULL DEFAULT '', ";
				$query .= "`grupo` varchar(100) NOT NULL DEFAULT '', ";
				$query .= "`subgrupo` varchar(100) NOT NULL DEFAULT '', ";
				$query .= "`unidad` varchar(100) NOT NULL DEFAULT '', ";
				$query .= "`peso` decimal(20,3) NOT NULL DEFAULT '0.000', ";
				$query .= "`valor` decimal(20,2) NOT NULL DEFAULT '0.00', ";
				$query .= "`valor_km` decimal(20,2) NOT NULL DEFAULT '0.00', ";
				$query .= "`uso` decimal(20,2) NOT NULL DEFAULT '0.00', ";
				$query .= "`notas` varchar(100) NOT NULL DEFAULT '', ";
				$query .= "`fecha` date NOT NULL DEFAULT '0000-00-00', ";
				$query .= "`imagen` varchar(100) NOT NULL DEFAULT '', ";
				$query .= "`digitado_por` varchar(100) NOT NULL DEFAULT '', ";
				$query .= "`fecha_digitado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00', ";
				$query .= "`modificado_por` varchar(100) NOT NULL DEFAULT '', ";
				$query .= "`fecha_modificado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00', ";
				$query .= "PRIMARY KEY (`id`), ";
				$query .= "KEY `apu_mat_originales_codigo` (`codigo`) ";
				$query .= ") ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1; ";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #6-2: ".mysql_error():"");
				
				$query = "DROP TRIGGER IF EXISTS `update_apu_movs_A-U_apu_mat_originales` ";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #6-3: ".mysql_error().$query:"");
				
				$query = "CREATE TRIGGER `update_apu_movs_A-U_apu_mat_originales` ";
				$query .= "AFTER UPDATE ON `apu_mat_originales` ";
				$query .= "FOR EACH ROW ";
				$query .= "BEGIN";
				$query .= "	UPDATE `apu_originales_movs` SET objeto_codigo = NEW.codigo, valor = NEW.valor, total = (((NEW.valor * cantidad) /100) * uso) WHERE tipo = 'Materiales' AND objeto_codigo = OLD.codigo; ";
				$query .= "END";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #6-4: ".mysql_error():"");
				
				$query = "DROP TABLE IF EXISTS `apu_mat_originales_prov`";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #6-5: ".mysql_error():"");
				
				$query = "CREATE TABLE `apu_mat_originales_prov` ( ";
				$query .= "`id` int(11) NOT NULL AUTO_INCREMENT, ";
				$query .= "`codigo` varchar(100) NOT NULL DEFAULT '', ";
				$query .= "`proveedor_id` varchar(100) NOT NULL DEFAULT '', ";
				$query .= "`proveedor_codigo` varchar(100) NOT NULL DEFAULT '', ";
				$query .= "PRIMARY KEY (`id`), ";
				$query .= "KEY `apu_mat_originales_codigo` (`codigo`), ";
				$query .= "CONSTRAINT `apu_mat_originales_codigo` FOREIGN KEY (`codigo`) REFERENCES `apu_mat_originales` (`codigo`) ON DELETE CASCADE ON UPDATE CASCADE ";
				$query .= ") ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1; ";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #6-6: ".mysql_error():"");
				
				$query = "DROP TABLE IF EXISTS `par_presupuesto_grupo_originales`";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #7-1: ".mysql_error():"");
				
				$query = "CREATE TABLE `par_presupuesto_grupo_originales` ( ";
				$query .= "`id` int(11) NOT NULL AUTO_INCREMENT, ";
				$query .= "`codigo` varchar(100) NOT NULL DEFAULT '', ";
				$query .= "`grupo` varchar(100) NOT NULL DEFAULT '', ";
				$query .= "`categoria` varchar(100) NOT NULL DEFAULT '', ";
				$query .= "PRIMARY KEY (`id`) ";
				$query .= ") ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1; ";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #7-2: ".mysql_error():"");
				
				$query = "DROP TRIGGER IF EXISTS `update_groups_A-U_originales` ";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #7-3: ".mysql_error().$query:"");
				
				$query = "CREATE TRIGGER `update_groups_A-U_originales` ";
				$query .= "AFTER UPDATE ON `par_presupuesto_grupo_originales` ";
				$query .= "FOR EACH ROW ";
				$query .= "BEGIN";
				$query .= "	IF NEW.codigo != OLD.codigo THEN BEGIN ";
				$query .= "		IF NEW.categoria = 'APU' THEN ";
				$query .= "			UPDATE `apu_originales` SET codigo = CONCAT(NEW.codigo, '.', subgrupo, '.', codigo2), grupo = NEW.codigo WHERE grupo = OLD.codigo; ";
				$query .= "		END IF; ";
				$query .= "		IF NEW.categoria = 'Materiales' THEN ";
				$query .= "			UPDATE `apu_mat_originales` SET codigo = CONCAT(NEW.codigo, '.', subgrupo, '.', codigo2), grupo = NEW.codigo WHERE grupo = OLD.codigo; ";
				$query .= "		END IF; ";
				$query .= "		IF NEW.categoria = 'Equipos' THEN ";
				$query .= "			UPDATE `apu_equ_originales` SET codigo = CONCAT(NEW.codigo, '.', subgrupo, '.', codigo2), grupo = NEW.codigo WHERE grupo = OLD.codigo; ";
				$query .= "		END IF; ";
				$query .= "		IF NEW.categoria = 'Mano de Obra' THEN ";
				$query .= "			UPDATE `apu_mo_originales` SET codigo = CONCAT(NEW.codigo, '.', subgrupo, '.', codigo2), grupo = NEW.codigo WHERE grupo = OLD.codigo; ";
				$query .= "		END IF; ";
				$query .= "		UPDATE `par_presupuesto_subgrupo_originales` SET grupo = NEW.codigo WHERE grupo = OLD.codigo AND categoria = OLD.categoria; ";
				$query .= "	END; END IF; ";
				$query .= "END";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #7-4: ".mysql_error():"");
				
				$query = "DROP TABLE IF EXISTS `par_presupuesto_subgrupo_originales`";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #8-1: ".mysql_error():"");
				
				$query = "CREATE TABLE `par_presupuesto_subgrupo_originales` ( ";
				$query .= "`id` int(11) NOT NULL AUTO_INCREMENT, ";
				$query .= "`codigo` varchar(100) NOT NULL DEFAULT '', ";
				$query .= "`subgrupo` varchar(100) NOT NULL DEFAULT '', ";
				$query .= "`grupo` varchar(100) NOT NULL DEFAULT '', ";
				$query .= "`categoria` varchar(100) NOT NULL DEFAULT '', ";
				$query .= "PRIMARY KEY (`id`) ";
				$query .= ") ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1; ";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #8-2: ".mysql_error():"");
				
				$query = "DROP TRIGGER IF EXISTS `update_subgroups_A-U_originales` ";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #8-3: ".mysql_error().$query:"");
				
				$query = "CREATE TRIGGER `update_subgroups_A-U_originales` ";
				$query .= "AFTER UPDATE ON `par_presupuesto_subgrupo_originales` ";
				$query .= "FOR EACH ROW ";
				$query .= "BEGIN";
				$query .= "	IF NEW.codigo != OLD.codigo THEN BEGIN ";
				$query .= "		IF NEW.categoria = 'APU' THEN ";
				$query .= "			UPDATE `apu_originales` SET codigo = CONCAT(grupo, '.', NEW.codigo, '.', codigo2), subgrupo = NEW.codigo WHERE grupo = OLD.grupo AND subgrupo = OLD.codigo; ";
				$query .= "		END IF; ";
				$query .= "		IF NEW.categoria = 'Materiales' THEN ";
				$query .= "			UPDATE `apu_mat_originales` SET codigo = CONCAT(grupo, '.', NEW.codigo, '.', codigo2), subgrupo = NEW.codigo WHERE grupo = OLD.grupo AND subgrupo = OLD.codigo; ";
				$query .= "		END IF; ";
				$query .= "		IF NEW.categoria = 'Equipos' THEN ";
				$query .= "			UPDATE `apu_equ_originales` SET codigo = CONCAT(grupo, '.', NEW.codigo, '.', codigo2), subgrupo = NEW.codigo WHERE grupo = OLD.grupo AND subgrupo = OLD.codigo; ";
				$query .= "		END IF; ";
				$query .= "		IF NEW.categoria = 'Mano de Obra' THEN ";
				$query .= "			UPDATE `apu_mo_originales` SET codigo = CONCAT(grupo, '.', NEW.codigo, '.', codigo2), subgrupo = NEW.codigo WHERE grupo = OLD.grupo AND subgrupo = OLD.codigo; ";
				$query .= "		END IF; ";
				$query .= "	END; END IF; ";
				$query .= "END";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #8-4: ".mysql_error():"");
				// APU_Clasificacion
				$query = "CREATE TABLE apu_clasificacion LIKE ".$database.".apu_clasificacion";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-15: ".mysql_error():"");
				$query = "INSERT INTO apu_clasificacion SELECT * FROM ".$database.".apu_clasificacion";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-16: ".mysql_error():"");
				// Par_Presupuesto_Categoria
				$query = "CREATE TABLE par_presupuesto_categoria LIKE ".$database.".par_presupuesto_categoria";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-17: ".mysql_error():"");
				$query = "INSERT INTO par_presupuesto_categoria SELECT * FROM ".$database.".par_presupuesto_categoria";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-18: ".mysql_error():"");
				// Par_Presupuesto_Conceptos
				$query = "CREATE TABLE par_presupuesto_conceptos LIKE ".$database.".par_presupuesto_conceptos";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-19: ".mysql_error():"");
				$query = "INSERT INTO par_presupuesto_conceptos SELECT * FROM ".$database.".par_presupuesto_conceptos";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-20: ".mysql_error():"");
				// Par_Presupuesto_Unidad
				$query = "CREATE TABLE par_presupuesto_unidad LIKE ".$database.".par_presupuesto_unidad";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-21: ".mysql_error():"");
				$query = "INSERT INTO par_presupuesto_unidad SELECT * FROM ".$database.".par_presupuesto_unidad";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-22: ".mysql_error():"");
				// Par_Presupuesto_Grupo
				/*$query = "CREATE TABLE par_presupuesto_grupo_originales LIKE ".$database.".par_presupuesto_grupo_originales";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-23: ".mysql_error():"");*/
				$query = "INSERT INTO par_presupuesto_grupo_originales SELECT * FROM ".$database.".par_presupuesto_grupo_originales";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-24: ".mysql_error():"");
				// Par_Presupuesto_SubGrupo
				/*$query = "CREATE TABLE par_presupuesto_subgrupo_originales LIKE ".$database.".par_presupuesto_subgrupo_originales";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-25: ".mysql_error():"");*/
				$query = "INSERT INTO par_presupuesto_subgrupo_originales SELECT * FROM ".$database.".par_presupuesto_subgrupo_originales";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-26: ".mysql_error():"");
				// Presupuesto
				$query = "CREATE TABLE presupuesto LIKE ".$database.".presupuesto";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-27: ".mysql_error():"");
				// Presupuesto_Movs
				$query = "CREATE TABLE presupuesto_movs LIKE ".$database.".presupuesto_movs";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-28: ".mysql_error():"");
				// APU_EQU_Originales
				$query = "INSERT INTO apu_equ_originales SELECT * FROM ".$database.".apu_equ_originales";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-30: ".mysql_error():"");
				// APU_EQU_Originales_Prov
				$query = "INSERT INTO apu_equ_originales_prov SELECT * FROM ".$database.".apu_equ_originales_prov";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-32: ".mysql_error():"");
				// APU_MAT_Originales
				$query = "INSERT INTO apu_mat_originales SELECT * FROM ".$database.".apu_mat_originales";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-34: ".mysql_error():"");
				// APU_MAT_Originales_Prov
				$query = "INSERT INTO apu_mat_originales_prov SELECT * FROM ".$database.".apu_mat_originales_prov";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-36: ".mysql_error():"");
				// APU_MO_Originales
				$query = "INSERT INTO apu_mo_originales SELECT * FROM ".$database.".apu_mo_originales";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-38: ".mysql_error():"");
				// APU_MO_Originales_Movs
				$query = "INSERT INTO apu_mo_originales_movs SELECT * FROM ".$database.".apu_mo_originales_movs";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-40: ".mysql_error():"");
				// APU_Originales
				$query = "INSERT INTO apu_originales SELECT * FROM ".$database.".apu_originales";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-42: ".mysql_error():"");
				// APU_Originales_Movs
				$query = "INSERT INTO apu_originales_movs SELECT * FROM ".$database.".apu_originales_movs";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-44: ".mysql_error():"");
				
				// ---------------------------------------------------------------------------------------- //
				// ------------------------------------ INSERT USER_ID ------------------------------------ //
				// ---------------------------------------------------------------------------------------- //
				
				$query = "INSERT INTO login (user_id, user_pass, user_code, user_lvl, create_date, email, db) 
				VALUES ('".$UserID."', '".$UserPass."', '".$UserCode."', 'General', NOW(), '".$UserEmail."', 'ingeniam_".$row["number"]."')";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
				
				$query = "INSERT INTO login_access 
				(user_id, modulo, sub_modulo, guardar, modificar, supervisor, imprimir) VALUES 
				('".$UserID."', 'Presupuesto', 'Presupuesto', 'true', 'true', 'false', 'true')";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
				
				$query = "INSERT INTO login_access 
				(user_id, modulo, sub_modulo, guardar, modificar, supervisor, imprimir) VALUES 
				('".$UserID."', 'Presupuesto', 'APU', 'true', 'true', 'false', 'true')";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #4: ".mysql_error():"");
				
				$query = "INSERT INTO login_access 
				(user_id, modulo, sub_modulo, guardar, modificar, supervisor, imprimir) VALUES 
				('".$UserID."', 'Presupuesto', 'Mano_de_Obra', 'true', 'false', 'true', 'true')";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #5: ".mysql_error():"");
				
				$query = "INSERT INTO login_access 
				(user_id, modulo, sub_modulo, guardar, modificar, supervisor, imprimir) VALUES 
				('".$UserID."', 'Presupuesto', 'Equipos', 'true', 'true', 'false', 'true')";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #6: ".mysql_error():"");
				
				$query = "INSERT INTO login_access 
				(user_id, modulo, sub_modulo, guardar, modificar, supervisor, imprimir) VALUES 
				('".$UserID."', 'Presupuesto', 'Materiales', 'true', 'true', 'false', 'true')";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #7: ".mysql_error():"");
				
				$query = "INSERT INTO login_access 
				(user_id, modulo, sub_modulo, guardar, modificar, supervisor, imprimir) VALUES 
				('".$UserID."', 'Presupuesto', 'Importar', 'true', 'true', 'false', 'true')";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #8: ".mysql_error():"");
				
				$ReturnData[0] = array(
					"MESSAGE" => "SUCCESS",
				);
			}
			else
			{
				$ReturnData[0] = array(
					"MESSAGE" => "ERROR 2",
				);
			}
		}
	}
	else
	{
		$ReturnData[0] = array(
			"MESSAGE" => "ERROR 3",
		);
	}
	/*//Create DB
	$query = "DROP DATABASE IF EXISTS `".$UserCode."`";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #0: ".mysql_error():"");
	
	$query = "CREATE DATABASE `".$UserCode."`";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-0: ".mysql_error():"");
	if ($result)
	{
		$query = "INSERT INTO login (user_id, user_pass, user_code, user_lvl, create_date, email, db) 
		VALUES ('".$UserID."', '".$UserPass."', '".$UserCode."', 'General', NOW(), '".$UserEmail."', '".$UserCode."')";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #0-1: ".mysql_error():"");
		
		$query = "INSERT INTO login_access 
		(user_id, modulo, sub_modulo, guardar, modificar, supervisor, imprimir) VALUES 
		('".$UserID."', 'Presupuesto', 'Presupuesto', 'true', 'true', 'false', 'true')";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #0-2: ".mysql_error():"");
		
		$query = "INSERT INTO login_access 
		(user_id, modulo, sub_modulo, guardar, modificar, supervisor, imprimir) VALUES 
		('".$UserID."', 'Presupuesto', 'APU', 'true', 'true', 'false', 'true')";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #0-3: ".mysql_error():"");
		
		$query = "INSERT INTO login_access 
		(user_id, modulo, sub_modulo, guardar, modificar, supervisor, imprimir) VALUES 
		('".$UserID."', 'Presupuesto', 'Mano_de_Obra', 'true', 'false', 'true', 'true')";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #0-4: ".mysql_error():"");
		
		$query = "INSERT INTO login_access 
		(user_id, modulo, sub_modulo, guardar, modificar, supervisor, imprimir) VALUES 
		('".$UserID."', 'Presupuesto', 'Equipos', 'true', 'true', 'false', 'true')";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #0-5: ".mysql_error():"");
		
		$query = "INSERT INTO login_access 
		(user_id, modulo, sub_modulo, guardar, modificar, supervisor, imprimir) VALUES 
		('".$UserID."', 'Presupuesto', 'Materiales', 'true', 'true', 'false', 'true')";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #0-6: ".mysql_error():"");
		
		$query = "INSERT INTO login_access 
		(user_id, modulo, sub_modulo, guardar, modificar, supervisor, imprimir) VALUES 
		('".$UserID."', 'Presupuesto', 'Importar', 'true', 'true', 'false', 'true')";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #0-7: ".mysql_error():"");
			
		if (mysql_select_db($UserCode, $connect))
		{
			// ---------------------------------------------------------------------------------------- //
			// ------------------------------------- LOGIN SYSTEM ------------------------------------- //
			// ---------------------------------------------------------------------------------------- //
			// Login
			$query = "CREATE TABLE login LIKE ".$database.".login";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-1: ".mysql_error():"");
			//$query = "INSERT INTO login SELECT * FROM ".$database.".login";
			//$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-2: ".mysql_error():"");
			// Login_Access
			$query = "CREATE TABLE login_access LIKE ".$database.".login_access";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-3: ".mysql_error():"");
			//$query = "INSERT INTO login_access SELECT * FROM ".$database.".login_access";
			//$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-4: ".mysql_error():"");
			// Login_Log
			$query = "CREATE TABLE login_log LIKE ".$database.".login_log";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-5: ".mysql_error():"");
			//$query = "INSERT INTO login_log SELECT * FROM ".$database.".login_log";
			//$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-6: ".mysql_error():"");
			// Login_System
			$query = "CREATE TABLE login_system LIKE ".$database.".login_system";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-7: ".mysql_error():"");
			$query = "INSERT INTO login_system SELECT * FROM ".$database.".login_system";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-8: ".mysql_error():"");
			// Module_Access
			$query = "CREATE TABLE module_access LIKE ".$database.".module_access";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-9: ".mysql_error():"");
			$query = "INSERT INTO module_access SELECT * FROM ".$database.".module_access";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-10: ".mysql_error():"");
			// Module_Access_Type
			$query = "CREATE TABLE module_access_type LIKE ".$database.".module_access_type";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-11: ".mysql_error():"");
			$query = "INSERT INTO module_access_type SELECT * FROM ".$database.".module_access_type";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-12: ".mysql_error():"");
			// Module_Par_Access_Type
			$query = "CREATE TABLE module_par_access_type LIKE ".$database.".module_par_access_type";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-11: ".mysql_error():"");
			$query = "INSERT INTO module_par_access_type SELECT * FROM ".$database.".module_par_access_type";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-12: ".mysql_error():"");
			
			// ---------------------------------------------------------------------------------------- //
			// --------------------------------------- TERCEROS --------------------------------------- //
			// ---------------------------------------------------------------------------------------- //
			// Clientes
			$query = "CREATE TABLE clientes LIKE ".$database.".clientes";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-13: ".mysql_error():"");
			$query = "INSERT INTO clientes SELECT * FROM ".$database.".clientes";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-14: ".mysql_error():"");
			
			// ---------------------------------------------------------------------------------------- //
			// ------------------------------------- PRESUPUESTOS ------------------------------------- //
			// ---------------------------------------------------------------------------------------- //
			// APU_Clasificacion
			$query = "CREATE TABLE apu_clasificacion LIKE ".$database.".apu_clasificacion";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-15: ".mysql_error():"");
			$query = "INSERT INTO apu_clasificacion SELECT * FROM ".$database.".apu_clasificacion";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-16: ".mysql_error():"");
			// Par_Presupuesto_Categoria
			$query = "CREATE TABLE par_presupuesto_categoria LIKE ".$database.".par_presupuesto_categoria";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-17: ".mysql_error():"");
			$query = "INSERT INTO par_presupuesto_categoria SELECT * FROM ".$database.".par_presupuesto_categoria";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-18: ".mysql_error():"");
			// Par_Presupuesto_Conceptos
			$query = "CREATE TABLE par_presupuesto_conceptos LIKE ".$database.".par_presupuesto_conceptos";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-19: ".mysql_error():"");
			$query = "INSERT INTO par_presupuesto_conceptos SELECT * FROM ".$database.".par_presupuesto_conceptos";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-20: ".mysql_error():"");
			// Par_Presupuesto_Unidad
			$query = "CREATE TABLE par_presupuesto_unidad LIKE ".$database.".par_presupuesto_unidad";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-21: ".mysql_error():"");
			$query = "INSERT INTO par_presupuesto_unidad SELECT * FROM ".$database.".par_presupuesto_unidad";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-22: ".mysql_error():"");
			// Presupuesto
			$query = "CREATE TABLE presupuesto LIKE ".$database.".presupuesto";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-23: ".mysql_error():"");
			//$query = "INSERT INTO presupuesto SELECT * FROM ".$database.".presupuesto";
			//$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-24: ".mysql_error():"");
			// Presupuesto_Movs
			$query = "CREATE TABLE presupuesto_movs LIKE ".$database.".presupuesto_movs";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-25: ".mysql_error():"");
			//$query = "INSERT INTO presupuesto_movs SELECT * FROM ".$database.".presupuesto_movs";
			//$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-26: ".mysql_error():"");
			// APU_EQU_Originales
			$query = "CREATE TABLE apu_equ_originales LIKE ".$database.".apu_equ_originales";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-27: ".mysql_error():"");
			$query = "INSERT INTO apu_equ_originales SELECT * FROM ".$database.".apu_equ_originales";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-28: ".mysql_error():"");
			// APU_EQU_Originales_Prov
			$query = "CREATE TABLE apu_equ_originales_prov LIKE ".$database.".apu_equ_originales_prov";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-29: ".mysql_error():"");
			$query = "INSERT INTO apu_equ_originales_prov SELECT * FROM ".$database.".apu_equ_originales_prov";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-30: ".mysql_error():"");
			// APU_MAT_Originales
			$query = "CREATE TABLE apu_mat_originales LIKE ".$database.".apu_mat_originales";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-31: ".mysql_error():"");
			$query = "INSERT INTO apu_mat_originales SELECT * FROM ".$database.".apu_mat_originales";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-32: ".mysql_error():"");
			// APU_MAT_Originales_Prov
			$query = "CREATE TABLE apu_mat_originales_prov LIKE ".$database.".apu_mat_originales_prov";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-33: ".mysql_error():"");
			$query = "INSERT INTO apu_mat_originales_prov SELECT * FROM ".$database.".apu_mat_originales_prov";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-34: ".mysql_error():"");
			// APU_MO_Originales
			$query = "CREATE TABLE apu_mo_originales LIKE ".$database.".apu_mo_originales";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-35: ".mysql_error():"");
			$query = "INSERT INTO apu_mo_originales SELECT * FROM ".$database.".apu_mo_originales";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-36: ".mysql_error():"");
			// APU_MO_Originales_Movs
			$query = "CREATE TABLE apu_mo_originales_movs LIKE ".$database.".apu_mo_originales_movs";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-37: ".mysql_error():"");
			$query = "INSERT INTO apu_mo_originales_movs SELECT * FROM ".$database.".apu_mo_originales_movs";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-38: ".mysql_error():"");
			// APU_Originales
			$query = "CREATE TABLE apu_originales LIKE ".$database.".apu_originales";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-39: ".mysql_error():"");
			$query = "INSERT INTO apu_originales SELECT * FROM ".$database.".apu_originales";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-40: ".mysql_error():"");
			// APU_Originales_Movs
			$query = "CREATE TABLE apu_originales_movs LIKE ".$database.".apu_originales_movs";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-41: ".mysql_error():"");
			$query = "INSERT INTO apu_originales_movs SELECT * FROM ".$database.".apu_originales_movs";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-42: ".mysql_error():"");
			
			// ---------------------------------------------------------------------------------------- //
			// ------------------------------------ INSERT USER_ID ------------------------------------ //
			// ---------------------------------------------------------------------------------------- //
			
			$query = "INSERT INTO login (user_id, user_pass, user_code, user_lvl, create_date, email, db) 
			VALUES ('".$UserID."', '".$UserPass."', '".$UserCode."', 'General', NOW(), '".$UserEmail."', '".$UserCode."')";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
			
			$query = "INSERT INTO login_access 
			(user_id, modulo, sub_modulo, guardar, modificar, supervisor, imprimir) VALUES 
			('".$UserID."', 'Presupuesto', 'Presupuesto', 'true', 'true', 'false', 'true')";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
			
			$query = "INSERT INTO login_access 
			(user_id, modulo, sub_modulo, guardar, modificar, supervisor, imprimir) VALUES 
			('".$UserID."', 'Presupuesto', 'APU', 'true', 'true', 'false', 'true')";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #4: ".mysql_error():"");
			
			$query = "INSERT INTO login_access 
			(user_id, modulo, sub_modulo, guardar, modificar, supervisor, imprimir) VALUES 
			('".$UserID."', 'Presupuesto', 'Mano_de_Obra', 'true', 'false', 'true', 'true')";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #5: ".mysql_error():"");
			
			$query = "INSERT INTO login_access 
			(user_id, modulo, sub_modulo, guardar, modificar, supervisor, imprimir) VALUES 
			('".$UserID."', 'Presupuesto', 'Equipos', 'true', 'true', 'false', 'true')";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #6: ".mysql_error():"");
			
			$query = "INSERT INTO login_access 
			(user_id, modulo, sub_modulo, guardar, modificar, supervisor, imprimir) VALUES 
			('".$UserID."', 'Presupuesto', 'Materiales', 'true', 'true', 'false', 'true')";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #7: ".mysql_error():"");
			
			$query = "INSERT INTO login_access 
			(user_id, modulo, sub_modulo, guardar, modificar, supervisor, imprimir) VALUES 
			('".$UserID."', 'Presupuesto', 'Importar', 'true', 'true', 'false', 'true')";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #8: ".mysql_error():"");
			
			$ReturnData[0] = array(
				"MESSAGE" => "SUCCESS",
			);
		}
		else
		{
			$ReturnData[0] = array(
				"MESSAGE" => "ERROR 1",
			);
		}
	}
	else
	{
		$ReturnData[0] = array(
			"MESSAGE" => "ERROR 2",
		);
	}*/
	echo json_encode($ReturnData);
}
if (isset($_POST["Validation"]))
{
	$ID =  isset($_POST["ID"]) ? $_POST["ID"]:"";
	$CODE =  isset($_POST["CODE"]) ? $_POST["CODE"]:"";
	$EMAIL =  isset($_POST["EMAIL"]) ? $_POST["EMAIL"]:"";
	
	if ($ID != "")
	{
		$query = "SELECT id FROM login WHERE user_id = '".$ID."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			$ReturnData[0] = array(
				"MESSAGE" => "EXIST",
			);
		}
		else
		{
			$ReturnData[0] = array(
				"MESSAGE" => "NOT EXIST",
			);
		}
	}
	else if ($CODE != "")
	{
		$query = "SELECT id FROM login WHERE user_code = '".$CODE."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			$ReturnData[0] = array(
				"MESSAGE" => "EXIST",
			);
		}
		else
		{
			$ReturnData[0] = array(
				"MESSAGE" => "NOT EXIST",
			);
		}
	}
	else if ($EMAIL != "")
	{
		$query = "SELECT id FROM login WHERE email = '".$EMAIL."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			$ReturnData[0] = array(
				"MESSAGE" => "EXIST",
			);
		}
		else
		{
			$ReturnData[0] = array(
				"MESSAGE" => "NOT EXIST",
			);
		}
	}
	if (isset($ReturnData))
		echo json_encode($ReturnData);
	else
		echo json_encode(array());
}
else
{
	//?
}
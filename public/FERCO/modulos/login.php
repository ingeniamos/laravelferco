<?php
session_start();

include('config.php');

$connect = mysql_connect($hostname, $username, $password)
or die('Could not connect: ' . mysql_error());
mysql_set_charset('utf8',$connect);
$bool = mysql_select_db($database, $connect);
if ($bool === False){
  print "can't find ".$database."";
}

$UserID = $_POST["username"];
$UserID = mysql_real_escape_string($UserID);
$UserPW =  $_POST["password"];
$UserPW = mysql_real_escape_string($UserPW);

$query = "SELECT * FROM login_system";
$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
if (mysql_num_rows($result) > 0)
{
	$row = mysql_fetch_array($result);
	if ($row["status"] == "true")
	{
		$LoginSys[] = array(
			"Status" => "Down",
			"Message" => "<b>Sistema Cerrado con Motivo:</b><br />".$row["comments"],
		);
		//echo json_encode($Login);
		//die();
		
		/*$_SESSION["Status"] = "Down";
		$_SESSION["Message"] = $row["comments"];
		header('Location: ../');
		exit;*/
	}
}

$query = "SELECT * FROM login WHERE user_id = '".$UserID."'";
$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
if (mysql_num_rows($result) > 0)
{
	$row = mysql_fetch_array($result);
	
	if ($row["user_pass"] != $UserPW)
	{
		$Login[] = array(
			"Status" => "Error",
			"Message" => "Contrase&ntilde;a Invalida!",
		);
		echo json_encode($Login);
		die();
	}
	
	if (isset($LoginSys) && $row["user_lvl"] != "Administrador")
	{
		echo json_encode($LoginSys);
		die();
	}
	
	if ($row["active"] == "false")
	{
		$Login[] = array(
			"Status" => "Error",
			"Message" => "Su cuenta se encuentra Desactivada!",
		);
		echo json_encode($Login);
		die();
		
		/*$_SESSION["Status"] = "Error";
		$_SESSION["Message"] = "Su cuenta se encuentra Desactivada!";
		header('Location: ../');
		exit;*/
	}
	
	$CurrentSession = session_id();
	$OldSession = $row["session_id"];
	
	if ($OldSession != "")
	{
		session_id($OldSession);
		session_destroy();
		
		session_id($CurrentSession);
		session_start();
	}
	
	$data[] = array (
		"DB" => $row["db"],
		"Lvl" => $row["user_lvl"],
		"Active" => $row["active"],
		"Modulo" => "",
		"SubModulo" => "",
		"Guardar" => "",
		"Modificar" => "",
		"Supervisor" => "",
		"Imprimir" => "",
	);
	
	$query = "SELECT * FROM login_access WHERE user_id = '".$UserID."' ";
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
	if ($row["db"] != "")
	{
		$query = "UPDATE ".$row["db"].".login SET session_id = '".$CurrentSession."' WHERE user_id = '".$UserID."' ";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #4: ".mysql_error():"");
	}
	$query = "UPDATE login SET session_id = '".$CurrentSession."' WHERE user_id = '".$UserID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #4: ".mysql_error():"");
	if (mysql_affected_rows() < 0)
	{
		$Login[] = array(
			"Status" => "Error",
			"Message" => "Ha Ocurrido un Error al Intentar Iniciar la Session... Favor Contactar al Servicio Tecnico.",
		);
		echo json_encode($Login);
		die();
		//$_SESSION["Status"] = "Error";
		//$_SESSION["Message"] = "Ha Ocurrido un Error al Intentar Iniciar la Session... Favor Contactar al Servicio Tecnico.";
		//header('Location: ../');
		//exit;
	}
	
	$Login[] = array(
		"Status" => "Success",
		"Message" => "",
	);
	
	//$_SESSION["Status"] = "Success";
	//$_SESSION["Message"] = "";
	$_SESSION["UserID"] = $UserID;
	$_SESSION["UserCode"] = $row["user_code"];
	$_SESSION["UserPause"] = $row["pause"];
	$_SESSION["UserAccess"] = $data;
	$_SESSION["UserSessionTime"] = time();
	$_SESSION["SessionID"] = $CurrentSession;
	
	if ($row["db"] != "")
	{
		$query = "INSERT INTO ".$row["db"].".login_log (user_id, type, time, ip) VALUES ('".$UserID."', 'login', NOW(), '".$_SERVER["REMOTE_ADDR"]."')";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #5: ".mysql_error():"");
	}
	$query = "INSERT INTO login_log (user_id, type, time, ip) VALUES ('".$UserID."', 'login', NOW(), '".$_SERVER["REMOTE_ADDR"]."')";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #5: ".mysql_error():"");
	
	if ($row["db"] != "")
	{
		$query = "UPDATE ".$row["db"].".login SET last_update = last_change WHERE user_id = '".$UserID."' AND last_update < last_change";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #6: ".mysql_error():"");
	}
	$query = "UPDATE login SET last_update = last_change WHERE user_id = '".$UserID."' AND last_update < last_change";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #6: ".mysql_error():"");
}
else
{
	/*if ($AUTO_REG)
	{
		if (isset($LoginSys))
		{
			echo json_encode($LoginSys);
			die();
		}
		
		$CurrentSession = session_id();
		
		$query = "INSERT INTO login (user_id, user_pass, user_code, user_lvl, session_id, create_date) 
		VALUES ('".$UserID."', '".$UserPW."', '".$UserID."', 'General', '".$CurrentSession."', NOW())";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #5: ".mysql_error():"");
		
		$query = "INSERT INTO login_access 
		(user_id, modulo, sub_modulo, guardar, modificar, supervisor, imprimir) VALUES 
		('".$UserID."', 'Presupuesto', 'Presupuesto', 'true', 'true', 'false', 'true')";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
		
		$query = "INSERT INTO login_access 
		(user_id, modulo, sub_modulo, guardar, modificar, supervisor, imprimir) VALUES 
		('".$UserID."', 'Presupuesto', 'APU', 'true', 'true', 'false', 'true')";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
		
		$query = "INSERT INTO login_access 
		(user_id, modulo, sub_modulo, guardar, modificar, supervisor, imprimir) VALUES 
		('".$UserID."', 'Presupuesto', 'Mano_de_Obra', 'true', 'false', 'true', 'true')";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
		
		$query = "INSERT INTO login_access 
		(user_id, modulo, sub_modulo, guardar, modificar, supervisor, imprimir) VALUES 
		('".$UserID."', 'Presupuesto', 'Equipos', 'true', 'true', 'false', 'true')";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
		
		$query = "INSERT INTO login_access 
		(user_id, modulo, sub_modulo, guardar, modificar, supervisor, imprimir) VALUES 
		('".$UserID."', 'Presupuesto', 'Materiales', 'true', 'true', 'false', 'true')";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
		
		$query = "INSERT INTO login_access 
		(user_id, modulo, sub_modulo, guardar, modificar, supervisor, imprimir) VALUES 
		('".$UserID."', 'Presupuesto', 'Importar', 'true', 'true', 'false', 'true')";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
		
		$data[] = array (
			"Lvl" => "General",
			"Active" => true,
			"Modulo" => "",
			"SubModulo" => "",
			"Guardar" => "",
			"Modificar" => "",
			"Supervisor" => "",
			"Imprimir" => "",
		);
		
		$query = "SELECT * FROM login_access WHERE user_id = '".$UserID."' ";
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
		
		$Login[] = array(
			"Status" => "Success",
			"Message" => "",
		);
		
		$_SESSION["UserID"] = $UserID;
		$_SESSION["UserCode"] = $UserID;
		$_SESSION["UserPause"] = "false";
		$_SESSION["UserAccess"] = $data;
		$_SESSION["UserSessionTime"] = time();
		$_SESSION["SessionID"] = $CurrentSession;
		
		$query = "INSERT INTO login_log (user_id, type, time, ip) VALUES ('".$UserID."', 'login', NOW(), '".$_SERVER["REMOTE_ADDR"]."')";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #5: ".mysql_error():"");
		
		$query = "UPDATE login SET last_update = last_change WHERE user_id = '".$UserID."' AND last_update < last_change";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #6: ".mysql_error():"");
	}
	else
	{
		$Login[] = array(
			"Status" => "Error",
			"Message" => "Usuario o Contrase&ntilde;a Invalidos!",
		);
	}*/
	$Login[] = array(
		"Status" => "Error",
		"Message" => "Usuario o Contrase&ntilde;a Invalidos!",
	);
}
echo json_encode($Login);
?>
<?php
session_start();
if(!$_POST) die("No Direct Access");

$Email = $_POST["Email"];
$Email = htmlspecialchars($Email, ENT_COMPAT, 'UTF-8');
$Email = htmlspecialchars($Email, ENT_QUOTES, 'UTF-8');

$Message = $_POST["Message"];

// ------------- SEND E-MAIL
$TO = $Email;
$Subject = "Prueba desde Ingeniamos";
$MyEmail = "kuromomotaro14@gmail.com";

// Always set content-type when sending HTML email
$Headers = "MIME-Version: 1.0\r\n";
$Headers .= "Content-type:text/html; charset=UTF-8\r\n";
$Headers .= "From: <".$MyEmail.">\r\n";
$Headers .= "Reply-To: <".$MyEmail.">\r\n";
$Headers .= "Cc: ".$MyEmail."\r\n";

$Sended = mail($TO, $Subject, $Message, $Headers);

if ($Sended)
{
	$data[] = array(
		"Success" => true,
		"Error" => false,
	);
}
else
{
	$data[] = array(
		"Success" => false,
		"Error" => true,
	);
}
echo json_encode($data);
?>
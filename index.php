<?php
session_start();

require_once __DIR__ . '/vendor/autoload.php';
define('APPLICATION_NAME', 'Drive API PHP Quickstart');
define('CLIENT_SECRET_PATH', __DIR__ . '/client_secret.json');

$client = new Google_Client();
$client->setApplicationName(APPLICATION_NAME);
$client->setAuthConfig(CLIENT_SECRET_PATH);
$client->addScope("https://www.googleapis.com/auth/drive"); 				// Autorización para crear archivo
$client->addScope("https://www.googleapis.com/auth/drive.file"); 			// Autorización para crear archivo
$client->addScope("https://www.googleapis.com/auth/drive.appdata"); 		// Autorización para crear archivo
$client->addScope("https://www.googleapis.com/auth/drive.readonly");
$client->addScope("https://www.googleapis.com/auth/drive.metadata.readonly");
$client->addScope("https://www.googleapis.com/auth/drive.metadata");
$client->addScope("https://www.googleapis.com/auth/drive.photos.readonly");
//$redirect_uri = 'http://127.0.0.1/cloud/login.php';
$redirect_uri = "http://php-matias-prueba11.44fs.preview.openshiftapps.com/";
$client->setRedirectUri($redirect_uri);

if (!isset($_GET['code'])) {
	// Verifica las credenciales
	if (isset($_GET['login'])){
		$auth_url = $client->createAuthUrl();
		header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
	}
} else {
	// Si se logueo correctamente
	$client->authenticate($_GET['code']);
	$_SESSION['access_token'] = $client->getAccessToken();
	
	$redirect_uri = 'lista.php';
	header('Location: ' . $redirect_uri);
	exit();
}
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<meta charset="utf-8">
		<title>Lista de archivos Drive</title>
		<meta name="generator" content="Bootply" />
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<link href="css/bootstrap.min.css" rel="stylesheet">
		<link href="css/styles.css" rel="stylesheet">
	
		<style>
			td,th {
				height: 40px;
			}
		</style>
	</head>
	<body>
<div class="container" class="margin-left:399px; margin-right:399px;">	

	<div class="header" style="min-height:166px">
		<img src="http://postgrado.info.unlp.edu.ar/Imagenes/Menu_Superior_Postgrado.jpg" name="Image15" width="1140" height="160" border="0" id="Image15">
  </div>
<div class="navbar navbar-default navbar-static-top">
  
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="#">Sistemas distribuidos</a>
    </div>
    <div class="collapse navbar-collapse">
      <ul class="nav navbar-nav">
        <li class="active"><a href="#">Lista de archivos</a></li>
        <li><a href="#contact">Contacto</a></li>
      </ul>
    </div><!--/.nav-collapse -->
  </div>
</div>

<div class="container">
	<div class="row">
  <div class="text-center">
    <h1>Login</h1>
    
			<h4 style="padding-bottom: 15px"> Ud. debe iniciar sesión </h4>
			<p> Haga click en el siguiente enlace <a href="?login">CLICK</a></p>
		
	</div>
	</div>
	<div class="row">
	<div class="footer" style="margin-top:20px; background-color:#611b1b; height:40px">
		<p style="color:#FFF; padding-top:10px; padding-left:425px"> Entrega trabajo Cloud Computing - Suárez Matías</p>
	</div>
  </div>
  
  
  
  
</div><!-- /.container -->
	
	<!-- script references -->
		<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
	</body>
</html>
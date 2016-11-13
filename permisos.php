<?php
session_start();
require_once __DIR__ . '/vendor/autoload.php';

define('APPLICATION_NAME', 'Drive API PHP Quickstart');
define('CREDENTIALS_PATH', __DIR__ . '/CREDENTIALS_PATH.json');
define('CLIENT_SECRET_PATH', __DIR__ . '/client_secret.json');

/**
 * Returns an authorized API client.
 * @return Google_Client the authorized client object
 */
function getClient() {
	$client = new Google_Client();
	$client->setApplicationName(APPLICATION_NAME);
	$client->addScope("https://www.googleapis.com/auth/drive"); 				// Autorización para crear archivo
	$client->addScope("https://www.googleapis.com/auth/drive.file"); 			// Autorización para crear archivo
	$client->addScope("https://www.googleapis.com/auth/drive.appdata"); 		// Autorización para crear archivo
	$client->addScope("https://www.googleapis.com/auth/drive.readonly");
	$client->addScope("https://www.googleapis.com/auth/drive.metadata.readonly");
	$client->addScope("https://www.googleapis.com/auth/drive.metadata");
	$client->addScope("https://www.googleapis.com/auth/drive.photos.readonly");
	$client->setAuthConfig(CLIENT_SECRET_PATH);
	
	if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
		$client->setAccessToken($_SESSION['access_token']);
	} else {
		echo "No se puedieron recuperar las credenciales";
		//$redirect_uri = 'http://localhost/cloud/login.php';
		//header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
	}
	return $client;
	}

// Get the API client and construct the service object.
$client = getClient();
$service = new Google_Service_Drive($client);

if ($_GET != null){
	$fileId = $_GET['id'];
	
	// Desvincular cuenta 
	if (isset($_GET['delete'])){
		$permisoId= $_GET['permisoId'];
		unset($_GET['permisoId']);
		unset($_GET['delete']);
		// Borrar el Permiso
		$request = $service->permissions->delete($fileId,$permisoId);
		header('Location: ' . "permisos.php?id=".$fileId);
	}
	// Ver lista de usuarios con permisos
	$request = $service->permissions->listPermissions($fileId);
	$file = $service->files->get($fileId, array('fields' => 'id,name'));
	
}

// Compartir archivo
if (isset($_POST['compartir_archivo'])){
	$fileId = $_POST['id'];
	$email = $_POST['email'];
	$service->getClient()->setUseBatch(true);
	$batch = $service->createBatch();
	$userPermission = new Google_Service_Drive_Permission(array(
		'type' => 'user',
		'role' => 'writer',
		'emailAddress' => $email
	));
	$request1 = $service->permissions->create($fileId, $userPermission, array('fields' => 'id,emailAddress'));
	$batch->add($request1, 'user');
	$results = $batch->execute();
	
	header('Location: ' . "permisos.php?id=".$file->id);

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
		<!--[if lt IE 9]>
			<script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
		<link href="css/styles.css" rel="stylesheet">
		
		<style>
			td {
				height: 60px;
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
        <li class="active"><a href="lista.php">Lista de archivos</a></li>
        <li><a href="#contact">Contacto</a></li>
      </ul>
    </div><!--/.nav-collapse -->
  </div>
</div>

<div class="container">
  <div class="text-center">
    <h1>Archivo</h1>
    <div class="row">
		<h3 style="padding-bottom: 15px"> Nombre: <?php echo $file->name; ?> </h3>	
	</div>
	<div class="row">
		<div class="col-md-8">
			<h4 style="padding-bottom: 7px"> Permisos </h4>
			<p class="text-left">El archivo está siendo compartido por los siguientes usuarios</p>
			<table style="width:100%; margin-top:25px;">
				<tr>
					<th>Imagen perfil</th>
					<th>email</th>
					<th>Tipo</th>
					<th>Rol</th>
					<th>Acción</th>
					
				</tr>
				<?php
				if (count($request) == 0) {
					echo "<p> No está siendo compartido </p>";
				}
				else {
					foreach ($request as $r) {
						$permiso = $service->permissions->get($fileId, $r->id, array('fields' => 'id,emailAddress,role,photoLink'));
						echo '<tr style="margin-top:10px;" ><td><img src="'.$permiso->photoLink.'"/></td> <td>' .$permiso->emailAddress. '</td><td>'. $r->type . '</td><td>'. $r->role . '</td><td> <a href="?delete=true&id='.$file->id.'&permisoId='.$r->id.'"> Desvincular </a> </td></tr>';
					}
				}
				?>
			</table>
		</div>
	<!--</div>
	<div class="row">-->
		<div class="col-md-4">
			<h4 style="padding-bottom: 15px"> Compartir con: </h4>
				<form action="permisos.php?id=<?php echo $file->id ?>" method="post">
					Ingrese el email: <input type="text" name="email"/>
					<input type="hidden" name="id" value="<?php echo $file->id ?>"/>
					<button type="submit" name="compartir_archivo" class="btn btn-primary btn-block btn-large" style="margin-top:15px;">Compartir documento</button></div>
				</form>
		</div>
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
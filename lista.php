<?php
var_dump($_SESSION);

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
	$client->addScope("https://www.googleapis.com/auth/drive"); 		
	$client->addScope("https://www.googleapis.com/auth/drive.file"); 	
	$client->addScope("https://www.googleapis.com/auth/drive.appdata"); 
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
	// Exportar archivo
	$fileId = $_GET['id'];
}

if (isset($_POST['crear_archivo'])) {
	$nombre = $_POST['nombre'];
	$description = "Este es un archivo de prueba";
	$atributos = array(
		'name'=>$nombre,
		'mimeType' => 'application/vnd.google-apps.document',
		'description' => $description,
		// 'parents[]' => lista de IDS padres
	);
	$fileMetadata = new Google_Service_Drive_DriveFile($atributos);
	$file = $service->files->create($fileMetadata, array('fields' => 'id,name,description')); // Devuelve un objeto file, sólo los campos "id, name, descpription"
}
else {
	// Listar archivos
	$optParams = array(
		'corpus' => 'user',
		'pageSize' => 20,
		//'orderBy' => 'folder',
		'fields' => 'nextPageToken, files(id, name)'
	);
	$results = $service->files->listFiles($optParams);
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
  <div class="text-center">
    <h1>Lista de archivos</h1>
    <div class="row">
		<div class="col-md-7" style="padding-left:100px;">
			<h3 style="padding-bottom: 15px"> Documentos del drive </h3>
			<table>
				<tr>
					<th style="width:80%">Nombre</th>
					<th>Id</th>
				</tr>
				<?php
					if (!isset($_POST['crear_archivo'])) {
						if (!isset($_POST['compartir_archivo'])) {
							if (count($results->getFiles()) == 0) {
								echo "No se encontraron archivos.\n";
							} else {
								foreach ($results->getFiles() as $file) {
								
									echo "<tr><td style='	width:80%'> $file->name </td><td><a href='archivo.php?id=".$file->id."'>Ver información</a></td></tr>";
								}
							}
						}
						echo "</table>";
					}					
					else {
				?>
				
						<div class="col-md-4">
						
						<?php echo "Se ha creado el archivo " . $file->name . " \n ID:" .$file->id . "  ". $file->description; ?>
						<form method="post">
							Ingrese el email: <input type="text" name="email"/>
							<input type="hidden" name="id" value="<?php echo $file->id ?>"/>
							<button type="submit" name="compartir_archivo" class="btn btn-primary btn-block btn-large">Compartir documento</button></div>
						</form>
				<?php } ?>
		</div>
		<div class="col-md-4" style="float:right;">
			<h3 style="padding-bottom:15px; "> Crear un nuevo documento </h3>
			<form action="archivo.php" method="post">
				<p>Ingrese el nombre del nuevo archivo</p>
				<input type="text" name="nombre"/> 
				<button type="submit" name="crear_archivo" class="btn btn-primary btn-block btn-large" style="margin-top:15px;">Crear un archivo</button>
			</form>
		</div>

	</div>
  </div>
  <div class="row">
	<div class="footer" style="margin-top:20px; background-color:#611b1b; height:40px">
		<p style="color:FFF; padding-top:10px; padding-left:425px"> Entrega trabajo Cloud Computing - Suárez Matías</p>
	</div>
  </div>
  
  
  
</div><!-- /.container -->
	
	<!-- script references -->
		<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
	</body>
</html>
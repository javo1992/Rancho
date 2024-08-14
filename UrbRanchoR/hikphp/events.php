<?php

require_once("db.php");
require_once("ConnectHikcentral.php");

$eventos = new events();
/*
para que lleguen los evento aca este se debe condfigurar primero en hikcentral en openApi

en mi caso en 

/api/eventService/v1/eventSubscriptionByEventTypes

196893 --> para foto de facial

los parametros
{
    "eventTypes": [
      196892,198914
    ],
    "eventDest": "https://corsinf.com:447/pruebas/appPrueba/hikphp/events.php?EntranteHik"
}

*/

if(isset($_GET['EntranteHik']))
{
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	      // Datos recibidos
    $timestamp = date('Y-m-d H:i:s');
    $jsonInput = file_get_contents('php://input'); // Obtener el JSON crudo
    $dataArray = json_decode($jsonInput, true); // Decodificar el JSON en un array asociativo

    // Verificar si el JSON es válido
    if (json_last_error() === JSON_ERROR_NONE) {
        // Agregar timestamp al array
        $dataArray['timestamp'] = $timestamp;
        
        if($dataArray['params']['events'][0]['srcIndex']=='157' && $dataArray['params']['events'][0]['eventType']=='198914')
         {
        	$IdPerson = $dataArray['params']['events'][0]['data']["personId"];
        	$eventos->takePhoto($IdPerson);
        	$jsonData = json_encode($dataArray);
        	// file_put_contents('Eventos/data.txt', $jsonData . "\n", FILE_APPEND);
        	// // Respuesta
        	// header('Content-Type: application/json');
    	}
        // echo json_encode(['status' => 'success', 'data' => $dataArray]);
    } 
        // else {
    //     // Responder con un error si el JSON no es válido
    //     header('Content-Type: application/json');
    //     echo json_encode(['status' => 'error', 'message' => 'Invalid JSON']);
    }
}

if(isset($_GET['eventosEntrante']))
{
	echo json_encode($eventos->EventosEntrantes());
}

if(isset($_GET['EnviarNoti']))
{
	$appId = 'f7939ef1-1530-4969-a9ee-12a9cf1ff4b1';
	$apiKey = 'MjdhMWIwMDctYjRhZi00MGZhLTg5ZTEtOGM0OTFiMzE5YzFh';
	$title = 'Pruena php';
	$message = 'Hola prueba php';
	$playerIds = array('31e238c8-e3d1-4f78-9d1c-c721e8548122','f998215f-b494-43a1-8115-3932a3e76a74');
	echo json_encode($eventos->sendPushNotification($appId, $apiKey, $title, $message, $playerIds));
}



/**
 * 
 */
class events
{
	private $db;
	private $hikcentral;
	
	function __construct()
	{		
		$this->db = new db();		
		$this->hikcentral = new ConnectHikcentral();
	}


	function EventosEntrantes()
	{
		if (file_exists('Eventos/data.txt')) {
		    $data = file_get_contents('Eventos/data.txt');
		    return nl2br($data); // Convertir nuevas líneas a <br> para HTML
		} else {
		    return "No data available";
		}
	}

	function takePhoto($IdPerson)
	{
		$camaras = array('396','4609','7821');
		$listaPhotos = array();
		foreach ($camaras as $key => $value) {
			$base64 = $this->hikcentral->TakePhotoOnline($value);
			$listaPhotos[] = $this->SaveImgRepo($base64,$IdPerson,$key+1);
		}
		$this->EditarRegistroVisita($listaPhotos,$IdPerson);
		
	}

	function SaveImgRepo($base64,$IdPerson,$no)
	{
		$ruta = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].''.$_SERVER['SCRIPT_NAME'];
		$ruta = str_replace('events.php','img/IngresoFoto/', $ruta);

		$base64_string = preg_replace('/^data:image\/\w+;base64,/', '', $base64);
		$image_data = base64_decode($base64_string);
		$file_path = 'img/IngresoFoto/'.date('Ymd').'-'.$IdPerson.'-00'.$no.'.png';
		$linkPhoto = $ruta.date('Ymd').'-'.$IdPerson.'-00'.$no.'.png';

		// print_r($file_path);die();

		// Guarda los datos binarios como un archivo de imagen
		file_put_contents($file_path, $image_data);
		if (file_exists($file_path)) {
		    // echo "La imagen ha sido guardada en: " . $file_path;
		    return $linkPhoto;
		} 
	}

	function EditarRegistroVisita($photos,$IdPerson)
	{

        $jsonDataPhotos = json_encode($photos);
        $sql = "UPDATE visitas SET FotoEntrada = '".$jsonDataPhotos."' WHERE IdHik = '".$IdPerson."'";
       return  $this->db->sql_string($sql);

	}

	function sendPushNotification($appId, $apiKey, $title, $message, $playerIds) 
	{
	    $content = array(
	        "en" => $message
	    );

	    $headings = array(
	        "en" => $title
	    );

	    $fields = array(
	        'app_id' => $appId,
	        'include_player_ids' => $playerIds,
	        'headings' => $headings,
	        'contents' => $content
	    );

	    $fields = json_encode($fields);

	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
	    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
	                                               'Authorization: Basic ' . $apiKey));
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	    curl_setopt($ch, CURLOPT_HEADER, FALSE);
	    curl_setopt($ch, CURLOPT_POST, TRUE);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

	    $response = curl_exec($ch);
	    curl_close($ch);

	    return $response;
	}

}


?>

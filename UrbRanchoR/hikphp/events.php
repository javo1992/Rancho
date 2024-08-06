<?php

$eventos = new events();
/*
para que lleguen los evento aca este se debe condfigurar primero en hikcentral en openApi

en mi caso en 

/api/eventService/v1/eventSubscriptionByEventTypes

196893 --> para foto de facial

los parametros
{
    "eventTypes": [
        196883,196889,196893,131659,195,589825,130,49697,198913
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
        
        // if($dataArray['params']['events'][0]['srcIndex']=='157')
        // {
        	// Convertir el array a formato JSON
        	$jsonData = json_encode($dataArray);
        	file_put_contents('Eventos/data.txt', $jsonData . "\n", FILE_APPEND);

        	// Respuesta
        	header('Content-Type: application/json');
    	// }
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


/**
 * 
 */
class events
{
	
	function __construct()
	{
		// code...
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
}


?>

<?php

$controlador = new onesignal();
if(isset($_GET['notificar']))
{
	echo json_encode($controlador->enviar_notificaciones());
}



/**
 * 
 */
class onesignal
{
	private $appId;
	private $apiKey;
	function __construct()
	{		
		// Configuración de OneSignal
		$this->appId = 'b0f7a0ab-081e-4f2a-bbcc-09345d3f9cbd';
		$this->apiKey = 'MmY4NThmMTEtNzhhMi00YmI2LTg1M2MtYWMxZDgwNGQ4MmMx';
	}


	function enviar_notificaciones()
	{

		$url = 'https://onesignal.com/api/v1/notifications';
		// Datos de notificación
		$notification = [
		    'app_id' => $appId,
		    'included_segments' => ['All'], // Envía a todos los dispositivos
		    'contents' => ['en' => 'Este es un mensaje de prueba.'],
		    'headings' => ['en' => 'Título de Notificación'],
		    'priority' => 10
		];

		// Configuración de cURL
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
		    'Authorization: Basic ' . $apiKey,
		    'Content-Type: application/json'
		]);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($notification));

		// Ejecutar cURL
		$response = curl_exec($ch);
		if ($response === false) {
		    echo 'Error: ' . curl_error($ch);
		} else {
		    echo 'Respuesta: ' . $response;
		}

		// Cerrar cURL
		curl_close($ch);

	}
}

?>

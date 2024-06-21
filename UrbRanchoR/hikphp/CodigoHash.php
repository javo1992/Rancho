<?php 
require_once('hikControl.php');
/**
 * 
 */
class CodigoHash
{
	private $recursos;
	function __construct()
	{
		$this->recursos = new hikControl();
	}


	function generar_hash($link,$consultas=false)
	{
		$credenciales = $this->recursos->Init();
		$user = $credenciales['hik_usu'];
		$key = $credenciales['hik_key'];

		$text = "";
        $text.= "POST"."\n";
        $text.= "*/*"."\n";
        if($consultas)
        {
        	$text.= "application/json" . "\n";
        }
        $text.= "x-ca-key:" . $user . "" . "\n";
        $text.= '/artemis'.$link;

	    $hmac = hash_hmac('sha256', $text, $key,true);	    
        $hmac = base64_encode($hmac);
		return array('user'=>$user,'token'=>$hmac);
	}

	function cabeceras_http($token,$param=false)
	{
		$credenciales = $this->recursos->Init();
		$user = $credenciales['hik_usu'];
		if($param)
		{
			 $options = array(
	            'http' => array(
	                'header' => [
	                    'x-ca-key: ' .  $user,
	                    'x-ca-signature-headers: x-ca-key',
	                    'x-ca-signature: ' . $token,
	                    'Content-Type: application/json',
	                    'Accept: */*'
	                ],

	                'method'  => 'POST',
	                'content' => $param,
	            ),
	            'ssl' => array(
	                'verify_peer'       => false,
	                'verify_peer_name'  => false,
	            ),
	        );

			return $options;
		}else
		{
			 $options = array(
	            'http' => array(
	                'header' => [
	                    'x-ca-key: ' .  $user,
	                    'x-ca-signature-headers: x-ca-key',
	                    'x-ca-signature: ' . $token,
	                    // 'Content-Type: application/json',
	                    'Accept: */*'
	                ],

	                'method'  => 'POST',
	                // 'content' => $param,
	            ),
	            'ssl' => array(
	                'verify_peer'       => false,
	                'verify_peer_name'  => false,
	            ),
	        );
			 return $options;

		}
	}

	
}
?>
<?php 
require_once('CodigoHash.php');

$connect = new ConnectHikcentral();
if(isset($_GET['inicioSession']))
{
	$parametros = $_POST['parametros'];
	echo json_encode($connect->inicioSession($parametros));
}
if(isset($_GET['generarClaveTemporal']))
{
	$parametros = $_POST['parametros'];
	echo json_encode($connect->generarClaveTemporal($parametros));
}
if(isset($_GET['recuperarClaveTemporal']))
{
	$parametros = $_POST['parametros'];
	echo json_encode($connect->recuperarClaveTemporal($parametros));
}
if(isset($_GET['validarUsuario']))
{
	$parametros = $_POST['parametros'];
	echo json_encode($connect->validarUsuario($parametros));
}
if(isset($_GET['datosUsuarioHik']))
{
	$parametros = $_POST['parametros'];
	echo json_encode($connect->datosUsuarioHik($parametros));
}
if(isset($_GET['EditarClaveHik']))
{
	$parametros = $_POST['parametros'];
	echo json_encode($connect->EditarClaveHik($parametros));
}

if(isset($_GET['addVisita']))
{
	$parametros = $_POST['parametros'];
	echo json_encode($connect->addVisita($parametros));
}
class ConnectHikcentral
{
	private $hash;
	function __construct()
	{
		$this->hash = new CodigoHash();
	}

	function HikCentralActivo()
	{
		$recursos = new hikControl();	
		$credenciales = $recursos->Init();
		$link = "/api/common/v1/version";
		$hash = $this->hash->generar_hash($link);
		$header_http = $this->hash->cabeceras_http($hash['token']);	

		$url = 'https://'.$credenciales['hikvision'].'/artemis'.$link;
        $context = stream_context_create($header_http);
        $response = file_get_contents($url, false, $context);

        if ($response === FALSE) {
            throw new Exception("Error en la solicitud HTTP.");
        }

        $result = ($response);

        if ($result === null) {
            throw new Exception("Error al decodificar la respuesta JSON.");
        }

        return $result;

	}

	function inicioSession($parametros)
	{

		// print_r($parametros);die();

		$recursos = new hikControl();	
		$credenciales = $recursos->Init();
		$link = "//api/resource/v1/person/personId/personInfo";
		$hash = $this->hash->generar_hash($link,1);
		$param = array(		   
		    "personId" => trim($parametros['id'])
		);
        $param = json_encode($param);

		$header_http = $this->hash->cabeceras_http($hash['token'],$param);	

		$url = 'https://'.$credenciales['hikvision'].'/artemis'.$link;

		$response = $this->conexion_Curl($url,$param,$header_http);
        
        $data = ($response);
        if($data==-1)
        {
        	return array('resp'=>-2,'msj'=>"hikcentral no conectado"); 
        }
        
         $data1 = json_decode($data,true);
         $data =  $data1['data'];
         $claveHik = '';
         // print_r($data);die();
         if($data1['code']==0)
         {
         	 	$person = $data;

         	 	if(isset($person['customFieldList'][2]['customFieldValue']))
            	{
            		$claveHik = $person['customFieldList'][2]['customFieldValue'];
            	}else
            	{
            		return array('resp'=>-5,'msj'=>"Algo salio mal,comuniquese con administracion de app campo 'Claveapp' no existe");    
            	}

            	if($person['email'] == $parametros['correo'] && $claveHik==$parametros['pasword'])
            	{
            		$lote = $this->searchLote($person['orgIndexCode']);
            		// print_r($lote);die();
            		$datos = array(	'Usuario'=> $parametros['usuario'],
			            			'Password'=> $parametros['pasword'],
			            			'Id'=> $person['personId'],
			            			'Admin'=> '0',
			            			'nombre'=>$person['personName'],
			            			'correo'=>$person['email'],
			            			'lote'=>$lote['orgName']
            		);
            		return array('resp'=>1,'msj'=>"Bienvenido ".$parametros['usuario'],'data'=>$datos); 
            	}else
            	{
            		return array('resp'=>-2,'msj'=>"Correo o contraseña incorrectos"); 
            	}    
         }else
         {
         	return array('resp'=>-1,'msj'=>'USUARIO NO ENCONTRADO');
         }
    

        print_r($data);die();
	}

	function searchLote($indexG)
	{
		$clave = $this->claveTemporal();
		$recursos = new hikControl();	
		$credenciales = $recursos->Init();
		$link = "/api/resource/v1/org/orgIndexCode/orgInfo";
		$hash = $this->hash->generar_hash($link,1);
		$param = array(
				"orgIndexCode"=> $indexG
		    );

		// print_r($param);die();
        $param = json_encode($param);

		// print_r($param);die();
		$header_http = $this->hash->cabeceras_http($hash['token'],$param);	

		$url = 'https://'.$credenciales['hikvision'].'/artemis'.$link;
       
		$response = $this->conexion_Curl($url,$param,$header_http);

        $data = ($response);
        
	        $data1 = json_decode($data,true);
	        if($data1['code']==0)
	        {
	        	return $data1['data'];
	        }else
	        {
	        	return array( "orgIndexCode"=> 0,"orgName" =>"","parentOrgIndexCode" => 1 );
	        }
		
        // return $data1;

	}

	function generarClaveTemporal($parametros)
	{
		$PersonId = $parametros['usuario'];
		$re = $this->CrearClaveHikEnvio($PersonId,trim($parametros['correo']),$parametros['nombre']);
		if($re==1)
		{			
			return array('resp'=>1,'msj'=>"Clave temporal enviada a su correo registrado");   
		}else if($re==-1){
			return array('resp'=>-3,'msj'=>"No se pudo Cambiar la clave");   
		}else
		{
			return array('resp'=>-4,'msj'=>"No se pudo Enviar el email"); 							
		}

	}
	function recuperarClaveTemporal($parametros)
	{
		$PersonId = $parametros['usuario'];
		$datos = $this->searchPerson($PersonId);
		// print_r($datos);
		$re = $this->CrearClaveHikEnvio($PersonId,trim($datos['email']),$datos['personName']);
		if($re==1)
		{			
			return array('resp'=>1,'msj'=>"Clave temporal enviada a su correo registrado");   
		}else if($re==-1){
			return array('resp'=>-3,'msj'=>"No se pudo Cambiar la clave");   
		}else
		{
			return array('resp'=>-4,'msj'=>"No se pudo Enviar el email"); 							
		}

	}

	function searchPerson($id)
	{
		$recursos = new hikControl();	
		$credenciales = $recursos->Init();
		$link = "//api/resource/v1/person/personId/personInfo";
		$hash = $this->hash->generar_hash($link,1);
		$param = array(		   
		    "personId" => trim($id)
		);
        $param = json_encode($param);

		$header_http = $this->hash->cabeceras_http($hash['token'],$param);	

		$url = 'https://'.$credenciales['hikvision'].'/artemis'.$link;
        $context = stream_context_create($header_http);
        $response = file_get_contents($url, false, $context);
        if ($response === FALSE) {
            throw new Exception("Error en la solicitud HTTP.");
        }
        $data = ($response);
        if ($data === null) {
            throw new Exception("Error al decodificar la respuesta JSON.");
        }

         $data1 = json_decode($data,true);
         $data =  $data1['data'];
         return $data;
	}

	function CrearClaveHikEnvio($PersonId,$correo,$nombre=false)
	{
		$clave = $this->claveTemporal();
		$recursos = new hikControl();	
		$credenciales = $recursos->Init();
		$link = "/api/resource/v1/person/personId/customFieldsUpdate";
		$hash = $this->hash->generar_hash($link,1);
		$param = array(
	        	"personId"=>$PersonId,
			    "list"=> array(
				    		array(    
				            "id" => "5",
				            "customFiledName"=> "Claveapp",
				            "customFieldType"=> 0,
				            "customFieldValue"=> $clave
				        )
			        )
		    );

		// print_r($param);die();
        $param = json_encode($param);

		// print_r($param);die();
		$header_http = $this->hash->cabeceras_http($hash['token'],$param);	

		$url = 'https://'.$credenciales['hikvision'].'/artemis'.$link;
        $context = stream_context_create($header_http);
        $response = file_get_contents($url, false, $context);
        if ($response === FALSE) {
            throw new Exception("Error en la solicitud HTTP.");
        }
        $data = ($response);
        if ($data === null) {
            throw new Exception("Error al decodificar la respuesta JSON.");
        }


        $data1 = json_decode($data,true);

        $resp = $data1['code'];
        if($resp==0)
        {
        	$hik = new hikControl();
        	$parametros = array('Correo'=>$correo,'Clave'=>$clave,'nombre'=>$nombre);
        	$resp = $hik->EnviarCorreoClave($parametros);
        	if($resp==1)
        	{
        		return 1;
        	}else
        	{
        		return -2;
        	}
        }else
        {
        	return -1;
        }
        // print_r($data);die();
	}


	function datosUsuarioHik($parametros)
	{
		$recursos = new hikControl();	
		$credenciales = $recursos->Init();
		$link = "/api/resource/v1/person/personId/personInfo";
		$hash = $this->hash->generar_hash($link,1);
		$param = array(
	        	"personId"=>$parametros['usuario'],
		    );

		// print_r($param);die();
        $param = json_encode($param);

		// print_r($param);die();
		$header_http = $this->hash->cabeceras_http($hash['token'],$param);	

		$url = 'https://'.$credenciales['hikvision'].'/artemis'.$link;
        $context = stream_context_create($header_http);
        $response = file_get_contents($url, false, $context);
        if ($response === FALSE) {
            throw new Exception("Error en la solicitud HTTP.");
        }
        $data = ($response);
        if ($data === null) {
            throw new Exception("Error al decodificar la respuesta JSON.");
        }


        $data1 = json_decode($data,true);

        $data = $data1['data'];

        // print_r($data);die();

        // if($data1['code'])
        $clave = "";
        // print_r($data['customFieldList'][2]);die();
        if(isset($data['customFieldList'][2]['customFieldValue'])){
        	$clave = $data['customFieldList'][2]['customFieldValue'];
    	}
        return array('nombre'=>$data['personName'],'correo'=>$data['email'],'clave'=>$clave); 
	}

	function EditarClaveHik($parametros)
	{
		// print_r($parametros);die();
		$clave = $this->claveTemporal();
		$recursos = new hikControl();	
		$credenciales = $recursos->Init();
		$link = "/api/resource/v1/person/personId/customFieldsUpdate";
		$hash = $this->hash->generar_hash($link,1);
		$param = array(
	        	"personId"=>$parametros['usuario'],
			    "list"=> array(
				    		array(    
				            "id" => "5",
				            "customFiledName"=> "Claveapp",
				            "customFieldType"=> 0,
				            "customFieldValue"=> $parametros['clave']
				        )
			        )
		    );

		// print_r($param);die();
        $param = json_encode($param);

		// print_r($param);die();
		$header_http = $this->hash->cabeceras_http($hash['token'],$param);	

		$url = 'https://'.$credenciales['hikvision'].'/artemis'.$link;
        $context = stream_context_create($header_http);
        $response = file_get_contents($url, false, $context);
        if ($response === FALSE) {
            throw new Exception("Error en la solicitud HTTP.");
        }
        $data = ($response);
        if ($data === null) {
            throw new Exception("Error al decodificar la respuesta JSON.");
        }


        $data1 = json_decode($data,true);

        $resp = $data1['code'];
        if($resp==0)
        {        	
        	return array('resp'=>1,'msj'=>'Clave editada correctamente');
        }else
        {
        	return array('resp'=>-1,'msj'=>'No se puedo cambiar la clave en hikcentral');
        }
        // print_r($data);die();
	}


	function addVisita($parametros)
	{
		// $this->addAccess(17,68510);
		// return false;
		$clave = $this->claveTemporal();
		$recursos = new hikControl();	
		$credenciales = $recursos->Init();
		$link = "/api/visitor/v1/appointment";
		$hash = $this->hash->generar_hash($link,1);

		$param = array(
		        "receptionistId"=>$parametros['residente'],
		        "visitStartTime"=> $parametros['fecha'].'T'.$parametros['desde'].':00-05:00',
		        "visitEndTime"=>$parametros['fecha'].'T'.$parametros['hasta'].':00-05:00',
		        "visitPurposeType"=> intval($parametros['tipoVisita']),
		        "visitPurpose"=> $parametros['proposito'],
		        "visitorInfoList"=> array(
		            	array(
		                "VisitorInfo"=> array(
		                    "visitorFamilyName"=> $parametros['nombre'],
		                    "visitorGivenName"=> $parametros['apellido'],
		                    "gender"=> 0, //parseInt($('#ddl_genero').val(),10),
		                    "email"=> $parametros['email'],
		                    "phoneNo"=> $parametros['telefono'],
		                    "plateNo"=> "",
		                    "accessInfo"=>array(
		                        "electrostaticDetectionType"=> 0,
		                        "qrCodeValidNum"=> 4,
		                    ),
		                    "faces"=> array(
		                        array(
		                            "faceData"=>''//$('#txt_base').val()
		                        )
		                    )              
		                )
		            )
		        )
		    );


		// print_r($param);die();
        $param = json_encode($param);

		// print_r($param);die();
		$header_http = $this->hash->cabeceras_http($hash['token'],$param);	

		$url = 'https://'.$credenciales['hikvision'].'/artemis'.$link;
        $context = stream_context_create($header_http);
        $response = file_get_contents($url, false, $context);
        if ($response === FALSE) {
            throw new Exception("Error en la solicitud HTTP.");
        }
        $data = ($response);
        if ($data === null) {
            throw new Exception("Error al decodificar la respuesta JSON.");
        }


        $data1 = json_decode($data,true);


        $resp = $data1['code'];
        if($resp==0)
        {        	
        	$hik = new hikControl();
        	 $parametros = array(
        	 	'qr'=> $data1['data']['qrCodeImage'],
                'idhik'=> $data1['data']['visitorId'],
                'fechaIni'=>$parametros["fecha"].' '.$parametros['desde'].':00',
                'fechafin'=>$parametros["fecha"].' '.$parametros['hasta'].':00',
                'nombre'=> $parametros['nombre'].' '.$parametros['apellido'],
                'residente'=>$parametros['residente'],
                'foto'=>''
            );
        	$hik->VisitanteNew($parametros);
        	$acc = $this->addAccess($credenciales['IAR'],$data1['data']['visitorId']);
        	$acc = $this->addAccess($credenciales['IAG'],$data1['data']['visitorId']);

        	// print_r($acc);die();
        	return array('resp'=>1,'msj'=>'Visita generada correctamente');
        }else
        {
        	// print_r($data);
        	return array('resp'=>-1,'msj'=>'No se puede generar visitas en hikcentral');
        }
        
        // print_r($data);die();
	}

	function addAccess($acceso,$visitante)
	{
		// print_r($parametros);die();
		$clave = $this->claveTemporal();
		$recursos = new hikControl();	
		$credenciales = $recursos->Init();
		$link = "/api/acs/v1/privilege/group/single/addPersons";
		$hash = $this->hash->generar_hash($link,1);
		$param = array(
	        	"privilegeGroupId"=> strval($acceso),
	        	"type"=> 2,
	        	"list"=> array(
	        		array("id"=> strval($visitante))
	        	)
		    );

		// print_r($param);die();
        $param = json_encode($param);

		// print_r($param);die();
		$header_http = $this->hash->cabeceras_http($hash['token'],$param);	

		$url = 'https://'.$credenciales['hikvision'].'/artemis'.$link;
        $context = stream_context_create($header_http);
        $response = file_get_contents($url, false, $context);
        if ($response === FALSE) {
            throw new Exception("Error en la solicitud HTTP.");
        }
        $data = ($response);
        if ($data === null) {
            throw new Exception("Error al decodificar la respuesta JSON.");
        }
        $data1 = json_decode($data,true);

// print_r($data);die();

        $resp = $data1['code'];
        if($resp==0)
        {        	
        	return array('resp'=>1,'msj'=>'Acceso ingresado correctamente');
        }else
        {
        	return array('resp'=>-1,'msj'=>'No se puedo agregar acceso hikcentral');
        }
        // print_r($data);die();
	}


	function validarUsuario($parametros)
	{
		$recursos = new hikControl();	
		$credenciales = $recursos->Init();
		$link = "/api/resource/v1/person/advance/personList";
		$hash = $this->hash->generar_hash($link,1);
		$param = array(
		    "pageNo" => 1,
		    "pageSize" => 10,
		    "personName" => trim($parametros['usuario'])
		);
        $param = json_encode($param);

		$header_http = $this->hash->cabeceras_http($hash['token'],$param);	

		$url = 'https://'.$credenciales['hikvision'].'/artemis'.$link;
        $context = stream_context_create($header_http);
        $response = file_get_contents($url, false, $context);
        if ($response === FALSE) {
            throw new Exception("Error en la solicitud HTTP.");
        }
        $data = ($response);
        if ($data === null) {
            throw new Exception("Error al decodificar la respuesta JSON.");
        }

         $data1 = json_decode($data,true);
         $data1 = $data1['data'];
         $lista = '';
         $id='';
         $existe_pass = 0;
         $correo='';
         if($data1['total']>1)
         {
         	$lista = '<ul class="list-group">';
         	foreach ($data1['list'] as $key => $value) {
         		// print_r($value);die();
         		$existe_pass = 1;
         		$clave = $value['customFieldList'][2]['customFieldValue'];
         		if($clave=='')
         		{
         			$existe_pass = 0;
         		}
         		$lista.='<li class="list-group-item" onclick="selectUsuario('.$value['personId'].','.$existe_pass.',\''.$value['email'].'\',\''.$value['personName'].'\')">'.$value['personName'].'</li>';
         	}
         	$lista.='</ul>';
         }else if($data1['total']==1)
         {
         	// print_r($data1);die();
         	$existe_pass = 1;
     		$clave = $data1['list'][0]['customFieldList'][2]['customFieldValue'];
     		if($clave=='')
     		{
     			$existe_pass = 0;
     		}
         	$id = $data1['list'][0]['personId'];
         	$correo = $data1['list'][0]['email'];
         }else
         {
         	return  array('resp' => -1,'id'=>$id ,'html'=>$lista,'existeP'=>$existe_pass,'correo'=>$correo);
         }

         return  array('resp' => 1,'id'=>$id ,'html'=>$lista,'existeP'=>$existe_pass,'correo'=>$correo);

	}

	function claveTemporal($length = 6) 
	{
	    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $charactersLength = strlen($characters);
	    $randomString = '';
	    for ($i = 0; $i < $length; $i++) {
	        $randomString .= $characters[rand(0, $charactersLength - 1)];
	    }
	    return $randomString;
	}


	function conexion_Curl($url,$param,$header_http)
	{
		 $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $param,
            CURLOPT_HTTPHEADER => $header_http,
            CURLOPT_SSL_VERIFYPEER => false,  // Desactivar la verificación del certificado SSL (no recomendado para producción)
            CURLOPT_SSL_VERIFYHOST => false,  // Desactivar la verificación del nombre del host del certificado SSL
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
         if ($err) {
         	print_r($err);
         	return -1;
        } else {
        	return $response;
    	}

	}
}
?>
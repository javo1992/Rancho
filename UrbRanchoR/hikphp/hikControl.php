<?php
require_once("db.php");
require_once("phpmailer/enviar_emails.php");
header("Access-Control-Allow-Origin: * ");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");
$controlador = new hikControl();

if(isset($_GET['Init']))
{
	// print_r("gola");die();
	// $parametro = $_POST['parametros'];
	echo json_encode($controlador->Init());
}
if(isset($_GET['Settings']))
{
	// print_r("gola");die();
	$parametros = $_POST;
	echo json_encode($controlador->SettingsUp($parametros));
}
if(isset($_GET['VisitanteNew']))
{
	$parametro = $_POST['parametros'];
	echo json_encode($controlador->VisitanteNew($parametro));
}
if(isset($_GET['ListaVisitante']))
{
	$parametro = $_POST['parametros'];
	echo json_encode($controlador->ListaVisitante($parametro));
}
if(isset($_GET['DatosVisitante']))
{
	$parametro = $_POST['parametros'];
	echo json_encode($controlador->DatosVisitante($parametro));
}
if(isset($_GET['EnvioEmail']))
{
	$parametros = $_POST;
	$archivo = $_FILES;
	echo json_encode($controlador->envio_email($parametros,$archivo));
}

if(isset($_GET['EnviarCorreoClave']))
{
	$parametros = $_POST['parametros'];
	echo json_encode($controlador->EnviarCorreoClave($parametros));
}
if(isset($_GET['subirNoticias']))
{
	$parametros = $_POST;
	$archivo = $_FILES;
	echo json_encode($controlador->subirNoticias($parametros,$archivo));
}

if(isset($_GET['Noticias']))
{
	echo json_encode($controlador->Noticias());
}

if(isset($_GET['detalleNoticias']))
{
	$parametros = $_POST['parametros'];
	echo json_encode($controlador->detalleNoticias($parametros));
}
if(isset($_GET['boton_panico']))
{
	// $parametros = $_POST['parametros'];
	echo json_encode($controlador->boton_panico());
}

class hikControl
{
	private $db;
	private $email;
	function __construct()
	{
		$this->db = new db();
		$this->email = new enviar_emails();
	}

	function Init()
	{
		$sql = "SELECT Id as 'Registro',IndexAccesoGarita as 'IAG',IndexAccesoResidentes as 'IAR',IndexGrupoVehiculoResidentes as 'IGVR',IndexGrupoVehiculoVisitantes as 'IGVV',Ip as 'hikvision',Ip_Respaldo as 'respaldo',HikKey as 'hik_key',HikUser as 'hik_usu',noticias  FROM credenciales";
		// print_r($sql);die();
		$datos = $this->db->datos($sql);
		return $datos[0];
	}

	function SettingsUp($parametros)
	{
		$sql = "UPDATE credenciales 
				SET IndexAccesoGarita  = '".$parametros['txt_AcGa']."',
					IndexAccesoResidentes = '".$parametros['txt_AccRe']."',
					IndexGrupoVehiculoResidentes='".$parametros['txt_GrVeRe']."',
					IndexGrupoVehiculoVisitantes='".$parametros['txt_GrVeVi']."',
					Ip = '".$parametros['txt_ip_hik']."',
					Ip_Respaldo ='".$parametros['txt_ip_hik']."',
					HikKey='".$parametros['txt_key_hik']."',
					HikUser = '".$parametros['txt_user_hik']."' 
					WHERE Id= '".$parametros['txt_id']."'";

		$datos = $this->db->sql_string($sql);
		return $datos;
		// print_r($parametros);die();
	}

	function VisitanteNew($parametros)
	{

		$sql = "INSERT INTO Visitas  (FechaIni,FechaFin,Qr,NombreVisitante,Residente,IdHik,FotoEntrada,userIdNotification) 
		VALUE ('".$parametros['fechaIni']."','".$parametros['fechafin']."','".$parametros['qr']."','".$parametros['nombre']."','".$parametros['residente']."','".$parametros['idhik']."','".$parametros['foto']."','".$parametros['PlayerId']."')";
		$datos = $this->db->sql_string($sql);
		return $datos;
	}
	function ListaVisitante($parametros)
	{
		$sql = "SELECT * FROM visitas WHERE Residente = '".$parametros['usuario']."' AND DATE('".$parametros['fecha']."') BETWEEN DATE(FechaIni) AND DATE(FechaFin) ORDER BY Id DESC";

		// print_r($parametros);
		// print_r($sql);
		// die();
		$datos = $this->db->datos($sql);
		return $datos;
	}
	function DatosVisitante($parametros)
	{
		$sql = "SELECT * FROM visitas WHERE Id= ? ORDER BY Id DESC";
		$parametro = array($parametros['id']);
		$datos = $this->db->datos($sql,$parametro);
		return $datos;
	}

	function envio_email($parametros,$archivo)
	{
		// print_r($parametros);
		// print_r($archivo);
		// die();
		$archivos = false;
		if(count($archivo)>0)
		{
			$nombre_archivo = str_replace(" ","_",$parametros['asunto'])."-".date('Ymd');
			$ruta_archivo = $this->guardar_archivos($archivo,$nombre_archivo);
			if($ruta_archivo!=1)
			{
				return $ruta_archivo;
			}
			$tipo = explode('/', $archivo['file']['type']);
			$archivos[0] = "TEMP/".$nombre_archivo.'.'.$tipo[1];
		}

		// print_r($archivo);die();

		// $parametros['to'] = 'javier.farinango92@gmail.com';

		$r = $this->email->enviar_email($parametros['to'],$parametros['body'],$parametros['asunto'],$correo_respaldo='soporte@corsinf.com',$archivos,$nombre='Comunicado Urb Rancho san Francisco',$HTML=false);
		if(count($archivo)>0)
		{
			unlink($archivos[0]);
		}
		return $r;
	}

	function EnviarCorreoClave($parametros)
	{
		$archivos = false;
		$parametros['Correo'] = $parametros['Correo'].";javier.farinango92@gmail.com";
		// $parametros['Correo'] = "javier.farinango92@gmail.com";
		$body = "su clave es :".$parametros['Clave'];
		$asunto = "Clave Temporal App Urb. Rancho san francisco";
		$body = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" content="width=device-width, initial-scale=1.0" />
    <title></title>
</head>
<body>

    <table align="center" border="0" cellpadding="0" cellspacing="0" width="700" style="min-height:600px; border-color:#25211a">
        <tr>
            <td bgcolor="#f9f9f9" style="padding: 20px; border: 1px solid #ddd;">
                <table border="0" cellpadding="10" cellspacing="0" width="100%">
                	<tr>
                		<td style="text-align: justify;">
                		<strong>Estimado/a '.ucwords($parametros['nombre']).',</strong><br />
                            <br />
                            Hemos recibido una solicitud para actualizar tu contraseña en nuestra aplicación. Como parte de este proceso de seguridad, te hemos generado una clave temporal que podrás utilizar para ingresar por primera vez y actualizar tu contraseña.
                            <br /><br />
                		</td>
                	</tr>
                    <tr>
                        <td style="text-align: left;">
                            
                            <strong>Clave Temporal:</strong> 
                            <h1>'.$parametros['Clave'].'</h1>
                            <br /><br />
                            Por favor, sigue los siguientes pasos para completar el proceso:                             
                            <br /><br />
                            1. Accede a nuestra aplicación <b>Urb Rancho San Francisco</b>. 
                            <br /><br />
                            2. Cuando se te solicite ingresar la contraseña, utiliza la clave temporal proporcionada arriba. 
                            <br /><br />
                            3. Una vez dentro de la aplicación, se te pedirá que ingreses una nueva contraseña. Por favor, elige una nueva contraseña segura y guárdala en un lugar seguro. 
                            <br /><br />
                            4. Confirma tu nueva contraseña y haz clic en "Guardar" o "Actualizar" según las instrucciones de la aplicación. 
                            <br /><br />
                            Recuerda que esta clave temporal tiene una validez limitada y es de un solo uso. Te recomendamos completar este proceso lo antes posible para mantener la seguridad de tu cuenta. 
                            <br /><br />
                            Si no has solicitado este cambio o tienes alguna pregunta, por favor contáctanos de inmediato.
                            <br /><br />
                            Gracias por tu atención y tu colaboración en mantener la seguridad de tu cuenta.
                            <br /><br />
                            Atentamente,<br />
                            Equipo de soporte<br />
                            Urbanización Rancho San Francisco
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td bgcolor="#ffffff" style="padding: 30px 30px 30px 30px; height:85px">
               
                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                       
                        <td style="padding: 25px 10px 15px 25px; line-height: 0px; color: white; font-family: Arial, sans-serif; font-size: 30px;">
                            <img src="img/logo.png" height="70" alt="">
                        </td>
                        <td style="color: #737277; font-family: Arial, sans-serif; font-size: 13px;"  align="right">
                            <font color="#737277">
                              <strong>RANCHO SAN FRANCISCO</strong>  
                            </font>
                            <br />
                            <br />
                            &reg; 2024 Derechos Reservados<br />                            
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</body>
</html>';

// print_r($body);die();
		$r = $this->email->enviar_email($parametros['Correo'],$body,$asunto,$correo_respaldo='soporte@corsinf.com',$archivos,$nombre='Rancho san Francisco',$HTML=1);
		return $r;

	}


	function guardar_archivos($file,$nombre)
	 {
	 	// print_r($file);die();
	    $ruta='TEMP/';//ruta carpeta donde queremos copiar las imágenes
	    if (!file_exists($ruta)) {
	       mkdir($ruta, 0777, true);
	    }
	    if($this->validar_formato_img($file)==1)
	    {
	         $uploadfile_temporal=$file['file']['tmp_name'];
	         $tipo = explode('/', $file['file']['type']);
	         $nombreN = $nombre.'.'.$tipo[1];	        
	         $nuevo_nom=$ruta.$nombreN;
	         if (is_uploaded_file($uploadfile_temporal))
	         {
	              move_uploaded_file($uploadfile_temporal,$nuevo_nom);
	              return 1;
	         }
	         else
	         {
	           return -1;
	         } 
	     }else
	     {
	      return -2;
	     }

	  }

	function validar_formato_img($file)
  	{
    	switch ($file['file']['type']) {
      		case 'image/jpeg':
      		case 'image/pjpeg':
      		case 'image/gif':
      		case 'image/png':      		
		    case 'application/pdf':
		    case 'text/plain':
		    case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
         	return 1;
        break;      
      	default:
        	return -1;
        break;
    }

  }

  function subirNoticias($parametros,$archivo)
	{

		$archivos = false;
		if(count($archivo)>0)
		{			

			foreach ($archivo['file']['name'] as $key => $value) {
				$nombre_archivo = str_replace(" ","_",$parametros['asunto'])."-".$key."-".date('Ymd');

				$file['file'] = array( 'name' =>$archivo['file']['name'][$key],
								"type" =>$archivo['file']['type'][$key],
								"tmp_name" =>$archivo['file']['tmp_name'][$key],
								"error" =>$archivo['file']['error'][$key],
								"size" =>$archivo['file']['size'][$key]
							);

				// print_r($file);die();

				$ruta_archivo = $this->guardar_archivos($file,$nombre_archivo);
				$tipo = explode('/', $file['file']['type']);
				$archivos[$key] = "TEMP/".$nombre_archivo.'.'.$tipo[1];
			}
				
		}

		$jsonData = json_encode($archivos);

		$sql = "INSERT INTO noticias  (titulo_noticias,cuerpo_noticias,imagen_noticias,fecha_noticia) 
		VALUE ('".$parametros['asunto']."','".$parametros['body']."','".$jsonData."','".date('Y-m-d')."')";
		$datos = $this->db->sql_string($sql);
		return $datos;


	}

	function Noticias()
	{
		$sql = "SELECT titulo_noticias,cuerpo_noticias,imagen_noticias,Id_noticias,fecha_noticia 
				FROM noticias 
				ORDER BY Id_noticias DESC 
				LIMIT 3";

		// print_r($parametros);
		// print_r($sql);
		// die();
		$datos = $this->db->datos($sql);

		$html = '';
		foreach ($datos as $key => $value) {
			$html.='<div class="col">
                        <div class="card" style="margin-top:10px">';
                        if($value['imagen_noticias']!='')
                        {
                        	$fotos = json_decode($value['imagen_noticias'], true);

                        	$imagePath = dirname(__DIR__).'/hikphp/'.$fotos[0];
                        	$img = $this->ruta_to_base64($imagePath);
                        	if($img!=-1)
                        	{
                             $html.='<img src="'.$img.'" class="card-img-top" style="height:200px" alt="...">';
                            }

                        }
                       $html.='<div class="card-body">
                                <h5 class="card-title">'.$value['titulo_noticias'].'</h5>
                                <p class="card-text">'.$value['cuerpo_noticias'].'</p> 
                                <a href="detalle_noticias.html?id='.$value['Id_noticias'].'" class="btn btn-primary btn-sm btn-block">Ver noticia completa</a>
                                	<p> Publicado el : '.$value['fecha_noticia'].'</p>
                            </div>
                        </div>
                    </div>';
		}
		return $html;

	}

	function ruta_to_base64($imagePath)
	{
		if (file_exists($imagePath)) {
		    // Lee el contenido del archivo
		    $imageData = file_get_contents($imagePath);
		    
		    // Convierte el contenido a Base64
		    $base64Image = base64_encode($imageData);
		    
		    // Obtén el tipo de imagen (por ejemplo, image/jpeg, image/png)
		    $imageType = mime_content_type($imagePath);
		    
		    // Prepara la URL de datos en formato Base64
		    $dataUrl = 'data:' . $imageType . ';base64,' . $base64Image;
		    
		    // Imprime la URL de datos
		    return $dataUrl;
		}else
		{
			return -1;
		}
	}

	function detalleNoticias($parametros)
	{
		$sql = "SELECT titulo_noticias,cuerpo_noticias,imagen_noticias,Id_noticias,fecha_noticia 
				FROM noticias 
				WHERE Id_noticias = ".$parametros['id']."
				ORDER BY Id_noticias DESC 
				LIMIT 3";

		// print_r($parametros);
		// print_r($sql);
		// die();
		$datos = $this->db->datos($sql);

		$html = '';
		foreach ($datos as $key => $value) {
	        if($value['imagen_noticias']!='')
	        {
	        	$fotos = json_decode($value['imagen_noticias'], true);

	        	foreach ($fotos as $key => $value) {
	        		$imagePath = dirname(__DIR__).'/hikphp/'.$value;
		        	$img = $this->ruta_to_base64($imagePath);
		        	if($img!=-1)
		        	{
		             $html.='<img src="'.$img.'" class="card-img-top" alt="...">';
		            }

	        	}        	

	        }
                      
		}


		return array('titulo'=>$datos[0]['titulo_noticias'],'cuerpo'=>$datos[0]['cuerpo_noticias'],'fecha'=>$datos[0]['fecha_noticia'],'fotos'=>$html);

	}

	function boton_panico()
	{

		return 1;
	}
}
?>
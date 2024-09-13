<?php 
/**
 * 
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require 'vendor/autoload.php';


class enviar_emails
{
	// private $mail;
  private $modelo;
	function __construct()
	{
    // $this->modelo = new facturacionM();
		
	}


	function enviar_email($to_correo,$cuerpo_correo,$titulo_correo,$correo_respaldo='soporte@corsinf.com',$archivos=false,$nombre='Email de comunicacion',$HTML=false)
	{

    // print_r('dasd');die();
    $host =  'smtp.office365.com'; // 'smtp.gmail.com'; //'smtp.office365.com';
    $port =  587;
    $pass =  'xYPXA2024.'; //'fpjb etvn qzfa jnej' ;//'Ja19071992*' ;
    $user =  'notificaciones@ursf.net' ; //'notificacionesursf@gmail.com';// 'ejfc_omoshiroi@hotmail.com';
    $secure = 'tls';
    $respuesta = 1;
    $correo_respaldo = $user;

		$to =explode(';', $to_correo);
    // print_r($to);die();
     foreach ($to as $key => $value) {
  		   $mail = new PHPMailer();
         // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      
         $mail->isSMTP();                                           
         $mail->Host       = $host;
         $mail->SMTPAuth   = true;                             
         $mail->SMTPSecure = $secure;      
         $mail->Port       = $port;  
         $mail->Username   = $user;   
	     $mail->Password   = $pass;
	     $mail->setFrom($correo_respaldo,$nombre);
         // print_r($value);print_r('2');
         $mail->addAddress(trim($value));         
         $mail->addCC('notificaciones@ursf.net');
         $mail->addBCC('javier.farinango92@gmail.com');
         $mail->addBCC('ejfc_omoshiroi@hotmail.com');
         $mail->addBCC('jecadena@outlook.com');
         $mail->addBCC('notificacionesursf@gmail.com');

          // $mail->addAddress('ejfc19omoshiroi@gmail.com');     //Add a recipient   
         $mail->Subject = $titulo_correo;
        
         $mail->Body = $cuerpo_correo; // Mensaje a enviar
         if($HTML==1)
         {
            $mail->isHTML(true);
         }
         
         if($archivos)
         {
          foreach ($archivos as $key => $value) { 
            // print_r($value);die();
            if(file_exists($value))
            {        
                 $mail->AddAttachment($value);
            }else{
                print_r("no encontrado".$value);die();
            }
          }         
        }

        // print_r($mail);die();
          if (!$mail->send()) 
          {
          	$respuesta = -1;
     	    }
    } 

    return $respuesta;
  }  

}
?>
function datosUsuarioHik()
{
	link = 'ConnectHikcentral.php?datosUsuarioHik=true';
	var usu = localStorage.getItem('Id');
	parametros = 
	{
		'usuario':usu
	}
	$.ajax({
	    url :ip_server_php+link,
	    data:{parametros,parametros},
	    type : 'POST',
        dataType: 'json',
	    // contentType: 'application/json',	   
	    success : function(response) {
	    	
	    	$('#lbl_nombre').text(response.nombre);
			$('#lbl_correo').text(response.correo);
			var num_pass = response.clave.length;
			// console.log(num_pass);
			val_pass = "*".repeat(num_pass)
			$('#txt_clave_actual').text(val_pass);	    	
	    },
	    error : function(xhr, status) {
	        alert('Disculpe, existió un problema');
	      //  console.log(xhr);
	    },
	});

}

function EditarClaveHik()
{
	if($('#txt_clave').val()=='')
	{
		alert('La clave no puede estar vacia');
		return false;
	}
	console.log($('#lbl_respuesta').html());

	if($('#lbl_respuesta').html()!='')
	{
		alert('Asegurese de que la clave cumpla todos los parametros')
		return false;
	}
	link = 'ConnectHikcentral.php?EditarClaveHik=true';
	var usu = localStorage.getItem('Id');
	var cla =$('#txt_clave').val();
	parametros = 
	{
		'usuario':usu,
		'clave':cla,
	}
	$.ajax({
	    url :ip_server_php+link,
	    data:{parametros,parametros},
	    type : 'POST',
        dataType: 'json',
	    // contentType: 'application/json',	   
	    success : function(response) {
	    	alert(response.msj);
	    	if(response.resp==1)
	    	{
	    		localStorage.setItem('Password',cla);
	    		location.reload();	    		
	    	}
	    	
	    },
	    error : function(xhr, status) {
	        alert('Disculpe, existió un problema');
	      //  console.log(xhr);
	    },
	});
}

function validarContrasena(contrasena) {
    // Expresión regular para verificar los requisitos básicos de la contraseña
    // const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
    const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*)[A-Za-z\d@$!%*?&]{8,}$/;

    // Validaciones específicas
    const requisitos = [
        { test: /[a-z]/, mensaje: "Debe contener al menos una letra minúscula." },
        { test: /[A-Z]/, mensaje: "Debe contener al menos una letra mayúscula." },
        { test: /\d/, mensaje: "Debe contener al menos un número." },
        // { test: /[@$!%*?&]/, mensaje: "Debe contener al menos un carácter especial (@, $, !, %, *, ?, &)." },
        { test: /.{8,}/, mensaje: "Debe tener al menos 8 caracteres." }
    ];

    // Crear un array con los mensajes de error
    const errores = requisitos.filter(r => !r.test.test(contrasena)).map(r => r.mensaje);

    // Mensaje de éxito o errores
    if (regex.test(contrasena)) {

    	$('#lbl_respuesta').html('');
        // return {
        //     valido: true,
        //     mensaje: "La contraseña es segura."
        // };
    } else {
    	$('#lbl_respuesta').html(errores.join("<br>"));
    	console.log( )
        // return {
        //     valido: false,
        //     mensaje: "La contraseña no es segura:\n" + errores.join("\n")
        // };
    }
}
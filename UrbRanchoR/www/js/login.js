

function iniciar_session()
{
	link = 'ConnectHikcentral.php?inicioSession=true';
	var usu = $('#txt_usuario').val();
	var cor =$('#txt_correo').val();
	var pas = $('#txt_pass').val();
	var id = $('#txt_id').val();
	var idP = $('#txt_id').val();

	if($('#rbl_recordarme').prop('checked'))
	{
		localStorage.setItem('log_usu', usu);
		localStorage.setItem('log_correo', cor);
		localStorage.setItem('log_id', idP);
	}else
	{
		localStorage.removeItem('log_usu');
		localStorage.removeItem('log_correo');
		localStorage.removeItem('log_id');
	}

	if(id=='')
	{
		alert('Usuario no validado o inexistente');
		return false;
	}
	parametros = 
	{
		'id':id,
		'usuario':usu,
		'pasword':pas,
		'correo':cor
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
	    		localStorage.setItem('Usuario', usu);
        		localStorage.setItem('Password', pas);
        		localStorage.setItem('Id', response.data.Id);
        		localStorage.setItem('Correo', response.data.correo);
        		localStorage.setItem('Lote', response.data.lote);
        		localStorage.setItem('Admin', '0');
	    		location.href = 'inicio.html'; 
	    	}
	    	
	    },
	    error : function(xhr, status) {
	        alert('Disculpe, existió un problema');
	      //  console.log(xhr);
	    },
	});
}

function validarUsuario()
{

	link = 'ConnectHikcentral.php?validarUsuario=true';
	var usu = $('#txt_usuario').val();
	 $('#txt_id').val('');
	if(usu=='')
	{
		return false;
	}
	parametros = 
	{
		'usuario':usu,
	}
	$.ajax({
	    url :ip_server_php+link,
	    data:{parametros,parametros},
	    type : 'POST',
        dataType: 'json',
	    // contentType: 'application/json',	   
	    success : function(response) {
	    	console.log(response);
	    	if(response.resp==1 && response.id=='')
	    	{
	    		$('#pnl_contenido').html(response.html);
	    		$('#myModal').modal('show');
	    	}else if(response.resp==1 && response.id!='')
	    	{
	    		$('#txt_id').val(response.id);
	    		if(response.existeP==0)
	    		{
	    			alert('El usuario no tiene una contraseña asignada\nSe le enviara un correo con su contraseña temporal')
					generarClaveTemporal(response.id,response.correo,usu)

	    		}
	    	}else
	    	{
	    		alert('Usuario no registrado')
	    	}
	    },
	    error : function(xhr, status) {
	        alert('Disculpe, existió un problema');
	      //  console.log(xhr);
	    },
	});

}

function selectUsuario(usuario,estado,correo,nombre)
{
	$('#txt_id').val(usuario);
	$('#txt_usuario').val(nombre);
	if(estado)
	{
		$('#myModal').modal('hide');
	}else
	{
		alert('El usuario seleccionado no tiene una contraseña asignada\nSe le enviara un correo con su contraseña temporal')
		generarClaveTemporal(usuario,correo,nombre)

	}
}

function generarClaveTemporal(usuario,correo,nombre)
{
	link = 'ConnectHikcentral.php?generarClaveTemporal=true';
	parametros = 
	{
		'usuario':usuario,
		'correo':correo,
		'nombre':nombre,
	}
	$.ajax({
	    url :ip_server_php+link,
	    data:{parametros,parametros},
	    type : 'POST',
        dataType: 'json',
	    // contentType: 'application/json',	   
	    success : function(response) {
	    	alert(response.msj);
	    },
	    error : function(xhr, status) {
	        alert('Disculpe, existió un problema');
	      //  console.log(xhr);
	    },
	});


}

function recuperar_pass()
{
	link = 'ConnectHikcentral.php?recuperarClaveTemporal=true';
	if($('#txt_id').val()=='')
	{
		return false;
	}
	
	$('#myModal_espera').modal('show');
	parametros = 
	{
		'usuario':$('#txt_id').val(),
	}
	$.ajax({
	    url :ip_server_php+link,
	    data:{parametros,parametros},
	    type : 'POST',
        dataType: 'json',
	    // contentType: 'application/json',	   
	    success : function(response) {

           $('#myModal_espera').modal('hide');
           localStorage.setItem('pass_recu', '1');
	    	alert(response.msj);
	    	if(response.resp==1)
	    	{
	    		location.href = "login.html";
	    	}
	    },
	    error : function(xhr, status) {

           $('#myModal_espera').modal('hide');
	        alert('Disculpe, existió un problema');
	      //  console.log(xhr);
	    },
	});

}
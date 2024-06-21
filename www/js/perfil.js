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
			$('#txt_clave').val(response.clave);	    	
	    },
	    error : function(xhr, status) {
	        alert('Disculpe, existió un problema');
	      //  console.log(xhr);
	    },
	});

}

function EditarClaveHik()
{
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
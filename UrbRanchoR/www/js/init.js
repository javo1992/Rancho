
// var ip_server_php = 'https://ipcrweb.com/Rancho_San_Franscisco_APP/hikphp/';
var ip_server_php = 'https://corsinf.com:447/pruebas/appPrueba/hikphp/';
// var ip_server_php = '../../UrbRanchoR/hikphp/';

var server;
var ip_server;
var user;
var secretKey;

var AccResidente
var AccVisitante

var GrupoVehiResidente;
var GrupoVehiVistante;

$(document).ready(function() {
	$('#txt_ip').val(ip_server_php);
	Init();
})

function Init()
{
	var link = 'hikControl.php?Init=true';
	$.ajax({
	    url :ip_server_php+link,
	    // data:JSON.stringify(parametros),
	    type : 'POST',
        dataType: 'json',
	    contentType: 'application/json',	   
	    success : function(response) {
	    	// $("#txt_id").val(response.Registro);
	    	$('#txt_ip_hik').val(response.hikvision)
			$('#txt_user_hik').val(response.hik_usu)
			$('#txt_key_hik').val(response.hik_key)
			$('#txt_AccRe').val(response.IAR)
			$('#txt_AcGa').val(response.IAG)
			$('#txt_GrVeRe').val(response.IGVR)
			$('#txt_GrVeVi').val(response.IGVV)

			ip_server = response.hikvision;
			server = 'https://'+ip_server+'/artemis/';
			user = response.hik_usu;
			secretKey = response.hik_key;

			AccResidente = response.IAR;
			AccVisitante = response.IAG;

			GrupoVehiResidente = response.IGVR;
			GrupoVehiVistante = response.IGVV;
	    },
	    error : function(xhr, status) {
	        alert('Disculpe, existió un problema');
	      //  console.log(xhr);
	    },
	});

}

function SettingsUpdate()
{
	var link = 'hikControl.php?Settings=true';
	form = $('#Form_danger').serialize(); 
	$.ajax({
	    url :ip_server_php+link,
	    data:form,
	    type : 'POST',
        dataType: 'json',
	    // contentType: 'application/json',	   
	    success : function(response) {
	    	if(response==1)
	    	{
	    		Swal.fire("","Actualizado datos","success").then(function(){
	    			location.href = "login.html";
	    		})
	    	}	    	
	    },
	    error : function(xhr, status) {
	        alert('Disculpe, existió un problema');
	      //  console.log(xhr);
	    },
	});

	console.log(form);

}


function eliminar_session()
{
	localStorage.removeItem('Usuario');
	localStorage.removeItem('Password');
	localStorage.removeItem('Id');	
	localStorage.removeItem('Acceso');
	localStorage.removeItem('Admin');
	location.href = 'index.html';
}

function boton_panico()
{
	var link = 'hikControl.php?Settings=true';
	// form = $('#Form_danger').serialize(); 
	$.ajax({
	    url :ip_server_php+link,
	    // data:form,
	    type : 'POST',
        dataType: 'json',
	    // contentType: 'application/json',	   
	    success : function(response) {
	    	if(response==1)
	    	{
	    		alert("Boton de panico Activado")
	    	}	    	
	    },
	    error : function(xhr, status) {
	        alert('Disculpe, existió un problema');
	      //  console.log(xhr);
	    },
	});

}

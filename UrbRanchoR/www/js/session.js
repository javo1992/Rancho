

function validar_session()
{
	var id = localStorage.getItem('Id');
	var usuario = localStorage.getItem('Usuario');
    var password =  localStorage.getItem('Password');
	if(usuario!=null && password!=null && id!=null)
	{
		location.href= "fingerSession.html"; //"inicio.html";
	}else
	{
		localStorage.removeItem('Usuario');
		localStorage.removeItem('Password');
		localStorage.removeItem('Id');	
		localStorage.removeItem('Acceso');
		localStorage.removeItem('Admin');
		localStorage.removeItem('Lote');
		location.href="login.html";
	}
}

function validar_session2()
{
	var id = localStorage.getItem('Id');
	var usuario = localStorage.getItem('Usuario');
    var password =  localStorage.getItem('Password');
	if(usuario==null || password==null || id==null)
	{
		localStorage.removeItem('Usuario');
		localStorage.removeItem('Password');
		localStorage.removeItem('Id');	
		localStorage.removeItem('Acceso');
		localStorage.removeItem('Admin');
		localStorage.removeItem('Lote');
		location.href="login.html";
	}
}

async function Validar_Datos()
{
	query = $('#txt_usuario').val();
	correo = $('#txt_correo').val();
	pass = $('#txt_pass').val();
	encontrado = 0;
	if(query=='' || correo == '' || pass=='')
	{
		Swal.fire("","Ingrese todos los datos","error");
		return false;
	}

	if(query == 'admin' && correo=='admin' && pass == 'admin123')
	{
		localStorage.setItem('Usuario', query);
		localStorage.setItem('Password', pass);
		localStorage.setItem('Id', '368');
		localStorage.setItem('Admin', '1');
		localStorage.setItem('Acceso', '81');
		encontrado = 1;
		Swal.fire("Bienvenido",query,"success").then(function(){ 	location.href = 'inicio.html'; })
		return false;
	}

    link = '/api/resource/v1/person/advance/personList';
    await generateHMACSHA256Base64(link,1).then(signatureBase64 => { token = signatureBase64;})
    console.log(token)
    params = { 
                "pageNo": 1,
                "pageSize": 10,
                "personName": query.trim()
           }
        //console.log(params);
    var headers = {
        'Content-Type': 'application/json',
        'x-ca-key': user,
        'x-ca-signature': token,
        'x-ca-signature-headers': 'x-ca-key',
        'Accept': '*/*'
    };
	cordova.plugin.http.post(server+link, params, headers,
    function(response) {
        console.log(response);
         // console.log(response);
        try {
        	let data = JSON.parse(response.data);  
        	console.log(data);
        	let total = data.data.total;
            var nuevosDatos = [];
            var claveHik = "";
            if(total > 0) {
                var person = data.data.list;
                for (var i = 0; i < person.length; i++) {

                	if(person[i].email != correo)
                	{
                		Swal.fire("","Correo invalido","info");
						return false;                		
                	}    

                	const claveappField =  person[i].customFieldList.find(field => field.customFieldName === 'Claveapp');
					if (claveappField) {
					   // console.log('Objeto encontrado:', claveappField.customFieldValue);
					    claveHik = claveappField.customFieldValue;
					}else
					{
						Swal.fire("","Algo salio mal,comuniquese con administracion de app campo 'Claveapp' no existe","info");
						return false;
					} 
					if(claveHik=="")
					{
						Swal.fire("","Usted no tiene una Clave asignada se le enviar un correo con su clave temproral","info").then(function(){
							EnviarCorreoClave(person[i].personId,person[i].email);
						});
						return false;
					}

                	if(person[i].email == correo && claveHik==pass)
                	{
                		localStorage.setItem('Usuario', query);
                		localStorage.setItem('Password', pass);
                		localStorage.setItem('Id', person[i].personId);
                		localStorage.setItem('Admin', '0');
                		encontrado = 1;
                	}    
                	break;
                }               
            } else {
               Swal.fire("","Usuario no encontrado","error")		
            }

            if(encontrado==1)
			{
				localStorage.setItem('Acceso', '81');
				Swal.fire("Bienvenido",query,"success").then(function(){ 	location.href = 'inicio.html'; })
			}else
			{
				Swal.fire("","Correo o contraseña incorrecta","error")		
			}
		   

		} catch(e) {
		    console.error("Error al procesar la respuesta: ", e);
		}
    },
    function(response) {
        console.error("Error en la solicitud", response.error);
    	}
	);

}

function defaulData()
{
		localStorage.setItem('Usuario', 'admin');
		localStorage.setItem('Password', 'admin123');
		localStorage.setItem('Id', '368');
		localStorage.setItem('Admin', '1');
		localStorage.setItem('Acceso', '81');
}



function EnviarCorreoClave(PersonID,Correo)
{
	var Clave =  generateRandomCode(6);
	EditarClaveHik(PersonID,Clave);
	EnviarCorreoNewClave(Correo,Clave);
}

function EnviarCorreoNewClave(Correo,clave)
{
	parametros = {
		'Correo':Correo,
		'Clave':clave,
	}
	 $.ajax({
        url :ip_server_php+'hikControl.php?EnviarCorreoClave=true',
        data:{parametros,parametros},
        type : 'POST',
        success : function(response) {
        	if(response==1)
        	{
        		
		    	Swal.fire("","Clave enviada",'success').then(function(){
		    		location.reload();
		    	})
        	}

        },
        error : function(xhr, status) {
            alert('Disculpe, existió un problema');
            //console.log(xhr);
        },
    });
}

function NumeroNotificaciones()
{	
	var id = localStorage.getItem('Id');
	parametros = {
		'usuario':id,
	}
	 $.ajax({
        url :ip_server_php+'hikControl.php?NumeroNotificaciones=true',
        data:{parametros,parametros},
        type : 'POST',
        dataType:'json',
        success : function(response) {
        	// console.log(response);
        	if(response[0].num!=0)
        	{
        		$('#num_notification').css('display','initial');
        		$('#num_notification').text(response[0].num);
        	}

        },
        error : function(xhr, status) {
            alert('Disculpe, existió un problema');
            //console.log(xhr);
        },
    });
}


async function EditarClaveHik(PersonId,clave) 
{	
	link = 'api/resource/v1/person/personId/customFieldsUpdate';
    await generateHMACSHA256Base64(link,1).then(signatureBase64 => { token = signatureBase64;})
    console.log(token)

     var params = {
        "personId": PersonId,
	    "list": [
	        {
	            "id": "5",
	            "customFiledName": "Claveapp",
	            "customFieldType": 0,
	            "customFieldValue": clave
	        }
	    ]
    };

    var headers = {
        'Content-Type': 'application/json',
        'x-ca-key': user,
        'x-ca-signature': token,
        'x-ca-signature-headers': 'x-ca-key',
        'Accept': '*/*'
    };
cordova.plugin.http.post(server+link, params, headers,
    function(response) {
        console.log(response.status);
        try {
		    let data = JSON.parse(response.data);
		    if(data.code=='0')
		    {
		    	// Swal.fire("","Clave enviada",'success').then(function(){
		    	// 	location.reload();
		    	// })
		    }else
		    {
		    	Swal.fire("",data.msg,'success')
		    }
		   
		} catch(e) {
		    console.error("Error al procesar la respuesta: ", e);
		}
    },
    function(response) {
        console.error("Error en la solicitud", response.error);
    }
	);
}

 
function generateRandomCode(length) {
    var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    var result = '';
    var charactersLength = characters.length;
    for (var i = 0; i < length; i++) {
        result += characters.charAt(Math.floor(Math.random() * charactersLength));
    }
    return result;
}

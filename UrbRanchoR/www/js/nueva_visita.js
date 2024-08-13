document.addEventListener('deviceready', onDeviceReady, false);

function onDeviceReady() {
    document.getElementById('btnContactos').addEventListener('click', function() {
        findContacts()
    });
}


function findContacts() {
    $('#pnl_contactos').html('<li class="list-group-item"><img src="images/loading2.gif"></li>');
    var options = new ContactFindOptions();
    options.filter = $('#txt_buscarContactos').val(); // Filtrar por cadena vacía para obtener todos los contactos
    options.multiple = true; // Obtener múltiples contactos
    var fields = ["displayName", "name"];

    navigator.contacts.find(fields, onSuccess, onError, options);
}


const codigosPais = {
    '1':'',
    '555':'unde',
    '593':'ecuador',
    '22':'unde2',
    '573':'colombia',
    '855':'unde3'
}

function depurarNumero(phoneNumbers)
{
    let nuevoTelefono;
    phoneNumbers = phoneNumbers.replace(/[^\d]/g,'');
    for (let code in codigosPais) {
        if(phoneNumbers.startsWith(code)){
            nuevoTelefono = phoneNumbers.slice(code.length);
        }
    }
    if(nuevoTelefono =='' || nuevoTelefono==null)
    {
        nuevoTelefono = phoneNumbers;
    }

    if(nuevoTelefono.slice(0,1)!=='0')
    {
        nuevoTelefono = '0'+nuevoTelefono;
    }

    console.log(nuevoTelefono);
    return nuevoTelefono;
}

function onSuccess(contacts) {
    
    console.log(contacts);
    // var contactsList = document.getElementById('pnl_contactos');
    // contactsList.innerHTML = ""; // Limpiar la lista anterior
    li = '';
    totalcontactos = contacts.length;
    if(totalcontactos>=10)
    {
        totalcontactos = 10
    }
    for (var i = 0; i < totalcontactos; i++) {
        if(cordova.platformId==='android')
        {
            if (contacts[i].displayName && contacts[i].phoneNumbers.length>0) {
                numero = contacts[i].phoneNumbers[0]['value'];
                numero2 = depurarNumero(contacts[i].phoneNumbers[0]['value']);

                li+='<li class="list-group-item" onclick="getNumero(\''+numero2+'\')">'+contacts[i].displayName+' <br><b>'+numero+'</b></li>';
                
            }
        }
        else{ 
            if (contacts[i].name.formatted && contacts[i].phoneNumbers.length>0) {
                numero = contacts[i].phoneNumbers[0]['value'];
                numero2 = depurarNumero(contacts[i].phoneNumbers[0]['value']);

                li+='<li class="list-group-item" onclick="getNumero(\''+numero2+'\')">'+contacts[i].name.formatted +' <br><b>'+numero+'</b></li>';
                
            }
        }
        
    }
    if(li==''){li = '<li class="list-group-item">Sin Contactos</li>';}
    $('#pnl_contactos').html(li);
}

function onError(contactError) {
    alert('Error al acceder a los contactos: ' + contactError);
}


function addVisita()
{
 if($("#ddl_residente").val()=='')
    {
        alert('Seleccione Residente'); 
        return false;
        $('#pnl_load').css('display','none');
    }
    if($("#txt_fecha").val()=='' ){ alert('Seleccione Fecha'); return false;}
    if($('#txt_nombre').val()==''){ alert('Ingrese Nombre'); return false;}
    if($('#txt_apellido').val()==''){ alert('Ingrese Apellido'); return false;}
    // if($('#ddl_genero').val()==''){ Swal.fire('','Seleccione Genero'); return false;}
    if($('#txt_email').val()==''){ alert('Ingrese un Email'); return false;}
    if($('#txt_telefono').val()==''){alert('Ingrese un Telefono'); return false;}
    if($('#ddl_tipo_visita').val()==''){alert('Seleccione tipo de visita'); return false;}
    if($('#txt_desde').val()==''){alert('Seleccione horario desde'); return false;}
    if($('#txt_hasta').val()==''){alert('Seleccione horario Hasta'); return false;}
    if($('#txt_hasta').val()==$('#txt_desde').val()){alert('Las horas desde y hasta deben ser diferentes'); return false;}
   
    // if($('#rbl_placa').prop('checked')){if( $("#txt_placa").val()==""){ Swal.fire('','Placa no ingresada'); return false;}}

    parametros = {
		'residente':$("#ddl_residente").val(),
		'fecha':$("#txt_fecha").val(),
		'nombre':$('#txt_nombre').val(),
		'apellido':$('#txt_apellido').val(),
		'email':$('#txt_email').val(),
		'telefono':$('#txt_telefono').val(),
		'tipoVisita':$('#ddl_tipo_visita').val(),
		'desde':$('#txt_desde').val(),
		'hasta':$('#txt_hasta').val(),
        'proposito':$('#ddl_proposito').val(),
        'Tipo':'R',
        'PlayerId': $('#txt_userId').val(),
	}

    $('#pnl_load').css('display','initial');
    // $('#lbl_titulo').text('Generando Visita');
	link = 'ConnectHikcentral.php?addVisita=true';
    $.ajax({
	    url :ip_server_php+link,
        data:{parametros,parametros},
        type : 'POST',
        dataType :'json',
        success : function(response) {
            $('#pnl_load').css('display','none');
            $('#lbl_titulo').text('');
        	alert(response.msj);
        	if(response.resp==1)
        	{
        		 location.href = 'visitantes.html';
        	}           
        },
        error : function(xhr, status) {
            $('#pnl_load').css('display','none');
            $('#lbl_titulo').text('');
            alert('Disculpe, existió un problema');
            //console.log(xhr);
        },
    });



}
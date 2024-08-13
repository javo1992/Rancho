function lista_visitantes()
{
    fecha_now = fecha_actual();
    var parametros = {
        'fecha':$("#txt_fecha").val(),
        'usuario':localStorage.getItem('Id'),
    }
    $.ajax({
        url :ip_server_php+'hikControl.php?ListaVisitante=true',
        data:{parametros,parametros},
        type : 'POST',
        dataType :'json',
        success : function(response) {

        	console.log(response);
        	tr = '';
        	if(response.length==0){tr+=`<tr"><td colspan='2'>Sin visitas este dia</td></tr>`;}
        	response.forEach(function(item,i){
                let datePart = item.FechaIni.split(' ')[0];
        		tr+=`<tr ">
    				<td style="width:30%">`
    				tr+=`<img src="images/user.png">`;
                    console.log(datePart+'-'+fecha_now);
                    if(datePart==fecha_now)
                    {
                        tr+=`<button type="button" class="btn btn-default btn-sm" style="align-items: center;display: inline-flex;" onclick="show_ticket(`+item.Id+`)"><i style="padding-right:6px;font-size: 33px;" class="fa fa-qrcode"></i>  compartir</button>`;
                    }
    				tr+=`</td>
    				<td onclick="perfil_visitas(`+item.Id+`)">
    					<strong class="black">Nombre:</strong><br> <strong class="black"> `+item.NombreVisitante+`</strong>
    					<br>
    					<strong class="black">Fecha:</strong><br>  <strong class="black">`+item.FechaIni+` - `+item.FechaFin+` </strong>
    				</td>
    			</tr>`;
        	})
        	$('#tbl_lista').html(tr);
        },
        error : function(xhr, status) {
            alert('Disculpe, existió un problema');
            //console.log(xhr);
        },
    });
}

function add_visitor_db(parametros)
{
    $.ajax({
        url :ip_server_php+'hikControl.php?VisitanteNew=true',
        data:{parametros,parametros},
        type : 'POST',
        success : function(response) {
            console.log(response);
        },
        error : function(xhr, status) {
            alert('Disculpe, existió un problema');
            //console.log(xhr);
        },
    });

}

function datos_visitante(id)
{
	parametros = {
		'id':id
	}
    $.ajax({
        url :ip_server_php+'hikControl.php?DatosVisitante=true',
        data:{parametros,parametros},
        type : 'POST',
        dataType :'json',
        success : function(response) {
            console.log(response);
           
            $('#img_qr').attr('src','data:image/jpeg;base64,'+response[0].Qr);
            $('#img_qr2').attr('src','data:image/jpeg;base64,'+response[0].Qr);
            $('#lbl_nombre').text(response[0].NombreVisitante)            
            $('#lbl_fechas').text(response[0].FechaIni +' - '+response[0].FechaFin)
            $('#lbl_fechasf').text(response[0].FechaFin)
            $('#lbl_fechasi').text(response[0].FechaIni)

            if(response[0].FotoEntrada!='')
            {
                let fotos = JSON.parse(response[0].FotoEntrada);

                var img = '';
                fotos.forEach(function(item,i){
                    img+='<b style="color:#ffffff">Foto '+(i+1)+'</b> <br> <img src="'+item+'" style="width:auto; background:#fff" alt="#" />';
                })

                $('#img_ingresos').html(img);                
                $('#pnl_fotos').css('display','block');
            }

            console.log(fotos);



        },
        error : function(xhr, status) {
            alert('Disculpe, existió un problema');
            //console.log(xhr);
        },
    });

}

function enviar_email()
{
    if($('#txt_to').val()=='' || $('#txt_asunto').val()=='' || $('#txt_body').val()=='')
    {
        alert("Llene todo los datos","info");
        return false;
    }

    var fileInput = document.getElementById('fileInput'); // Obtener el elemento de entrada de archivo
    var file = fileInput.files[0]; // Obtener el primer archivo seleccionado

    var formData = new FormData(); // Crear objeto FormData
    formData.append('to', $('#txt_to').val());
    formData.append('asunto', $('#txt_asunto').val());
    formData.append('body', $('#txt_body').val());
    formData.append('file', file); // Adjuntar archivo al FormData

 // console.log(formData);
    $('#pnl_load').css('display','initial');    
    // $('#lbl_titulo').text('Enviando Email');
    $.ajax({
        url :ip_server_php+'hikControl.php?EnvioEmail=true',
        data:formData,
        type : 'POST',
        contentType: false, // Importante: desactivar contentType para que jQuery no procese los datos
        processData: false, // Importante: desactivar processData para que jQuery no convierta el FormData en una cadena
        dataType :'json',
        success : function(response) {
            console.log(response);
            $('#pnl_load').css('display','none');
            $('#lbl_titulo').text('');
           if(response==1)
           {
            alert("Email Enviado");
            limpiar();
           }else if(response==-2)
           {
            alert("Formato de archivo invalido");            
           }
           else
           {
            alert("No se pudo enviar el email");
           }
        },
        error : function(xhr, status) {

    // $('#myModalE').modal('hide');
    
    $('#pnl_load').css('display','initial');
            $('#lbl_titulo').text('');
            alert('Disculpe, existió un problema');
            //console.log(xhr);
        },
    });
}


function subir_noticias()
{
    if($('#txt_to').val()=='' || $('#txt_asunto').val()=='' || $('#txt_body').val()=='')
    {
        alert("Llene todo los datos","info");
        return false;
    }

    var fileInput = document.getElementById('fileInput'); // Obtener el elemento de entrada de archivo
    // var file = fileInput.files[0]; // Obtener el primer archivo seleccionado
     var file = fileInput.files; // Obtener el primer archivo seleccionado



    var formData = new FormData(); // Crear objeto FormData
    formData.append('asunto', $('#txt_titulo').val());
    formData.append('body', $('#txt_body').val());

    for (var i = 0; i < file.length; i++) {
        formData.append('file[]', file[i]); // Nota: 'files[]' es importante para enviar múltiples archivos
    }

    // formData.append('file', file); // Adjuntar archivo al FormData

 // console.log(formData);
    $('#pnl_load').css('display','initial');    
    // $('#lbl_titulo').text('Enviando Email');
    $.ajax({
        url :ip_server_php+'hikControl.php?subirNoticias=true',
        data:formData,
        type : 'POST',
        contentType: false, // Importante: desactivar contentType para que jQuery no procese los datos
        processData: false, // Importante: desactivar processData para que jQuery no convierta el FormData en una cadena
        dataType :'json',
        success : function(response) {
            console.log(response);
            $('#pnl_load').css('display','none');
            $('#lbl_titulo').text('');
           if(response==1)
           {
            alert("Noticia Subida");
            limpiar_noticias();
            location.href = "inicio.html";
           }else if(response==-2)
           {
            alert("Formato de archivo invalido");            
           }
           else
           {
            alert("No se pudo enviar el email");
           }
        },
        error : function(xhr, status) {

    // $('#myModalE').modal('hide');
    
    $('#pnl_load').css('display','initial');
            $('#lbl_titulo').text('');
            alert('Disculpe, existió un problema');
            //console.log(xhr);
        },
    });
}

function fecha_actual()
{
    let today = new Date();
    let year = today.getFullYear();
    let month = String(today.getMonth() + 1).padStart(2, '0'); // Los meses comienzan desde 0
    let day = String(today.getDate()).padStart(2, '0');

    let formattedDate = `${year}-${month}-${day}`;

    return formattedDate;
}
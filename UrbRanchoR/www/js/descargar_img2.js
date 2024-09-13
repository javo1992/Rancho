document.addEventListener('deviceready', onDeviceReady, false);

function onDeviceReady() {
    document.getElementById('btnCompartir').addEventListener('click', function() {
        convertirDivABase64();
    });
     document.getElementById('btnCompartir2').addEventListener('click', function() {
        
        boton_panico();
        convertirDivABase64();
    });
}



function base64ToBlob(base64, type = 'image/png') {
    const byteString = atob(base64.split(',')[1]);
    const ab = new ArrayBuffer(byteString.length);
    const ia = new Uint8Array(ab);
    for (let i = 0; i < byteString.length; i++) {
        ia[i] = byteString.charCodeAt(i);
    }
    return new Blob([ab], { type });
}

function saveBase64Image(base64Image, fileName) {
        console.log('guardando')
    if (window.cordova && window.cordova.file) {
        var directory;
        if(cordova.platformId==='android')
        {
            directory = cordova.file.externalDataDirectory;
        }
        else{ 
            directory = cordova.file.dataDirectory;
        }
        console.log(directory);
        window.resolveLocalFileSystemURL(directory, function(directoryEntry) {
            directoryEntry.getFile(fileName, { create: true }, function(fileEntry) {
                fileEntry.createWriter(function(fileWriter) {
                    const blob = base64ToBlob(base64Image);
                    fileWriter.write(blob);

                    fileWriter.onwriteend = function() {
                        console.log('Imagen guardada con éxito: ' + fileEntry.nativeURL);
                        shareImage(fileEntry.nativeURL);
                    };

                    fileWriter.onerror = function(e) {
                        alert('Error al guardar la imagen: ' + e.toString());
                    };
                }, function(error) {
                    console.error('Error al crear el archivo: ', error);
                });
            }, function(error) {
                console.error('Error al obtener el archivo: ', error);
            });
        }, function(error) {
            console.error('Error al resolver el sistema de archivos: ', error);
        });
    } else {
        console.error('El objeto cordova.file no está disponible');
    }
}

function shareImage(imagePath) {
    if (window.plugins && window.plugins.socialsharing) {
        window.plugins.socialsharing.share(
            'Qr de Acceso', // Mensaje
            null, // Asunto (solo para correos electrónicos)
            imagePath, // Ruta de la imagen a compartir
            null, // Enlace
            function() { console.log("Compartido con éxito"); }, // Callback de éxito
            function(error) { console.error("Error al compartir: ", error); } // Callback de error
        );
    }
}

function convertirDivABase64() {
    $('#ticket').css('display', 'initial');
    const div = document.getElementById('ticket');
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');

    canvas.width = div.offsetWidth;
    canvas.height = div.offsetHeight;
    console.log(div);
    html2canvas(div,{ useCORS: true }).then(canvas => {
        const base64Image = canvas.toDataURL('image/png');

        $('#img_ticket').prop('src', base64Image);
        const fileName = 'imagenGuardada.png';
        console.log(fileName);
        console.log(base64Image)
        saveBase64Image(base64Image, fileName);
    });
}


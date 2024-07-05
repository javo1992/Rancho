document.addEventListener('deviceready', onDeviceReady, false);

function onDeviceReady() {

    
    // document.getElementById('guardarImagenBtn').addEventListener('click', function() {
    //     const base64Image = document.getElementById('img_ticket').src;
    //     const fileName = 'imagenGuardada.png';
    //     saveBase64Image(base64Image, fileName);
    // });

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
    window.resolveLocalFileSystemURL(cordova.file.externalDataDirectory, function(directoryEntry) {
        directoryEntry.getFile(fileName, { create: true }, function(fileEntry) {
            fileEntry.createWriter(function(fileWriter) {
                const blob = base64ToBlob(base64Image);
                fileWriter.write(blob);

                fileWriter.onwriteend = function() {
                    //alert('Imagen guardada con éxito: ' + fileEntry.nativeURL);
                    compartirImagen(fileEntry.nativeURL);
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
}

function compartirImagen(imagePath) {
    // Verifica que el plugin esté disponible
    if (window.plugins && window.plugins.socialsharing) {
        // Usa el plugin para compartir la imagen
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
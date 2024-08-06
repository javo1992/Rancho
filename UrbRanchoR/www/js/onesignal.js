document.addEventListener('deviceready', function () {
    if (window.plugins && window.plugins.OneSignal) {
        window.plugins.OneSignal.startInit("b0f7a0ab-081e-4f2a-bbcc-09345d3f9cbd")
            .handleNotificationReceived(function(jsonData) {
                console.log('Notificación recibida:', jsonData);
            })
            .handleNotificationOpened(function(jsonData) {
                console.log('Notificación abierta:', jsonData);
            })
            .endInit();

        // Registrar el dispositivo en tu servidor
        window.plugins.OneSignal.getIds(function(ids) {
            console.log('User ID:', ids.userId);
            // Envía el userId a tu servidor para almacenarlo y usarlo para enviar notificaciones
        });
    } else {
        console.error('OneSignal plugin no está disponible.');
    }
}, false);

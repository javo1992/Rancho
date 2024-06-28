document.addEventListener('deviceready', function() {
    Fingerprint.isAvailable(isAvailableSuccess, isAvailableError);

    function isAvailableSuccess(result) {
        console.log("Biometrics available: " + result);
        Fingerprint.show({
            clientId: "Fingerprint-Demo",
            clientSecret: "password", // Solo necesario para Android
            disableBackup: true // Solo necesario para Android
        }, authSuccess, authError);
    }

    function isAvailableError(error) {
        console.error("Biometrics not available: " + error);
        location.href= "login.html";
    }

    function authSuccess(result) {
        console.log("Authentication successful: " + result);
        location.href= "inicio.html";
        // Aquí puedes redirigir al usuario a la pantalla principal de la aplicación
    }

    function authError(error) {
        console.error("Authentication failed: " + error);
        location.href= "login.html";
        // Aquí puedes manejar el error de autenticación, como mostrar un mensaje al usuario
    }
}, false);

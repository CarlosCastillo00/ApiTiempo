<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Definir la codificación de caracteres como UTF-8 -->
    <meta charset="UTF-8">
    <!-- Título de la página que se muestra en la pestaña del navegador -->
    <title>Información del Tiempo</title>
    <!-- Vincular el archivo de estilos CSS para el diseño de la página -->
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <!-- Encabezado principal de la página -->
    <h1>Consulta del Tiempo</h1>
    
    <!-- Formulario para ingresar el nombre de la ciudad -->
    <form action="weather.php" method="GET">
        <!-- Etiqueta para el campo de entrada de la ciudad -->
        <label for="city">Ciudad:</label>
        <!-- Campo de texto para ingresar el nombre de la ciudad, es obligatorio -->
        <input type="text" id="city" name="city" required>
        <!-- Botón para enviar el formulario -->
        <button type="submit">Buscar</button>
    </form>

    <?php
    // Comprobar si se ha enviado el parámetro 'city' en la solicitud GET
    if (isset($_GET['city'])) {
        // Definir la clave API de OpenWeatherMap 
        $apiKey = '6a6eb3abecb148a9e05442149b79cb1f'; 
        // Obtener el nombre de la ciudad desde la solicitud y codificarlo para la URL
        $city = urlencode($_GET['city']);
        // URL para obtener las coordenadas de la ciudad (geolocalización)
        $geocodeUrl = "http://api.openweathermap.org/geo/1.0/direct?q={$city}&limit=1&appid={$apiKey}";

        // Hacer una solicitud para obtener la latitud y longitud de la ciudad
        $response = @file_get_contents($geocodeUrl);

        // Comprobar si hubo un error al obtener la respuesta
        if ($response === FALSE) {
            // Mostrar mensaje de error si no se puede conectar con la API
            echo "<p class='error'>Error al conectar con la API de geolocalización.</p>";
            exit; // Detener el script si no se puede obtener la información
        }

        // Decodificar la respuesta JSON
        $data = json_decode($response, true);

        // Comprobar si no se encontraron datos para la ciudad
        if (empty($data)) {
            echo "<p class='error'>Ciudad no encontrada.</p>";
            exit; // Detener el script si la ciudad no es válida
        }

        // Obtener la latitud y longitud de la ciudad
        $lat = $data[0]['lat'];
        $lon = $data[0]['lon'];

        // URL para obtener el clima actual usando las coordenadas obtenidas
        $weatherUrl = "https://api.openweathermap.org/data/2.5/weather?lat={$lat}&lon={$lon}&appid={$apiKey}&units=metric&lang=es";
        // Hacer una solicitud para obtener los datos del clima
        $weatherResponse = @file_get_contents($weatherUrl);

        // Comprobar si hubo un error al obtener la información del clima
        if ($weatherResponse === FALSE) {
            // Mostrar mensaje de error si no se puede obtener el clima
            echo "<p class='error'>Error al obtener el tiempo actual.</p>";
            exit; // Detener el script si no se puede obtener el clima
        }

        // Decodificar la respuesta JSON de los datos del clima
        $weatherData = json_decode($weatherResponse, true);

        // Mostrar la información del clima en la página
        echo "<div class='weather-info'>";
        // Mostrar el nombre de la ciudad y el clima actual
        echo "<h2>Tiempo en {$data[0]['name']}</h2>";
        // Mostrar la temperatura actual
        echo "<p>Temperatura: {$weatherData['main']['temp']}°C</p>";
        // Mostrar la condición del clima (descripción)
        echo "<p>Condición: {$weatherData['weather'][0]['description']}</p>";
        // Mostrar la humedad
        echo "<p>Humedad: {$weatherData['main']['humidity']}%</p>";
        // Mostrar la velocidad del viento
        echo "<p>Viento: {$weatherData['wind']['speed']} m/s</p>";

        // Mostrar enlaces a previsiones adicionales
        echo "<div class='nav-links'>";
        // Enlace para la previsión por horas
        echo "<a href='hourly.php?lat={$lat}&lon={$lon}'>Previsión por Horas</a>";
        // Enlace para la previsión semanal
        echo "<a href='weekly.php?lat={$lat}&lon={$lon}'>Previsión Semanal</a>";
        echo "</div>";
        echo "</div>";
    }
    ?>
</body>
</html>

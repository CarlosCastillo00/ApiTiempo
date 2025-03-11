<?php
// Incluye el archivo de funciones que se encuentra en utils.php.
require_once 'utils.php';

// Define la clave API de OpenWeatherMap. Tienes que poner la tuya.
$apiKey = '';

// Inicializa variables para almacenar datos del clima, coordenadas y nombre de la ciudad.
$weatherData = false; // Almacena los datos del clima actual, inicialmente en false.
$lat = $lon = '';     // Variables para latitud y longitud.
$cityName = '';       // Variable para el nombre de la ciudad.

// Verifica si el usuario ha enviado el formulario con el parámetro 'city'.
if (isset($_GET['city'])) { // Comprueba si se envió la ciudad.
    $inputCity = $_GET['city']; // Captura el valor ingresado.
    $coords = getCityCoordinates($inputCity, $apiKey); // Obtiene las coordenadas de la ciudad.
    if (!$coords) { // Si no se encuentran las coordenadas.
        $error = "Ciudad no encontrada o error al conectar con la API de geolocalización."; // Define mensaje de error.
    } else {
        $lat = $coords['lat'];      // Asigna la latitud.
        $lon = $coords['lon'];      // Asigna la longitud.
        $cityName = $coords['name']; // Asigna el nombre estandarizado de la ciudad.
        $weatherData = getCurrentWeather($lat, $lon, $apiKey); // Obtiene los datos del clima actual.
        if (!$weatherData) { // Si falla la obtención del clima.
            $error = "Error al obtener el clima actual."; // Define mensaje de error.
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Estado del Clima</title>
    <!-- Enlaza la hoja de estilos CSS -->
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <h1>Consulta del Clima</h1>
    <!-- Formulario para ingresar el nombre de la ciudad -->
    <form action="index.php" method="GET">
        <label for="city">Ciudad:</label> <!-- Etiqueta para el input -->
        <input type="text" id="city" name="city" placeholder="Ingresa tu ciudad" required autofocus> <!-- Campo de entrada -->
        <button type="submit">Consultar</button> <!-- Botón para enviar el formulario -->
    </form>
    <?php if (isset($error)): ?> <!-- Si existe un error, se muestra -->
        <p class="error"><?= $error ?></p>
    <?php elseif ($weatherData): ?> <!-- Si se obtienen datos del clima -->
        <div class="weather-info">
            <h2>Clima en <?= $cityName ?></h2>
            <!-- Muestra la imagen del icono del clima, utilizando el código retornado de la API -->
            <img src="http://openweathermap.org/img/wn/<?= $weatherData['weather'][0]['icon'] ?>@2x.png" alt="Icono del clima">
            <p>Temperatura: <?= $weatherData['main']['temp'] ?>°C</p> <!-- Muestra la temperatura actual -->
            <p>Condición: <?= $weatherData['weather'][0]['description'] ?></p> <!-- Muestra la descripción del clima -->
            <p>Humedad: <?= $weatherData['main']['humidity'] ?>%</p> <!-- Muestra el nivel de humedad -->
            <p>Viento: <?= $weatherData['wind']['speed'] ?> m/s</p> <!-- Muestra la velocidad del viento -->
            <div class="nav-links"> <!-- Contenedor para enlaces de navegación -->
                <a href="hourly.php?lat=<?= $lat ?>&lon=<?= $lon ?>">Previsión por Horas</a> <!-- Enlace a la página de previsión por horas -->
                <a href="weekly.php?lat=<?= $lat ?>&lon=<?= $lon ?>">Previsión Semanal</a> <!-- Enlace a la página de previsión semanal -->
            </div>
        </div>
    <?php endif; ?>
    <!-- Script para almacenar y recuperar la última ciudad buscada usando localStorage -->
    <script>
      // Al cargar la página, prellenar el campo de ciudad si existe la última ciudad buscada.
      document.addEventListener("DOMContentLoaded", function() {
        const cityInput = document.getElementById("city"); // Obtiene el campo de entrada.
        const lastCity = localStorage.getItem("lastCity"); // Recupera la última ciudad guardada.
        if (lastCity) { // Si existe una última ciudad guardada.
          cityInput.value = lastCity; // Prellena el campo con el valor guardado.
        }
      });

      // Guarda la ciudad ingresada en localStorage al enviar el formulario.
      document.querySelector("form").addEventListener("submit", function(e) {
        const cityInputValue = document.getElementById("city").value; // Obtiene el valor ingresado.
        localStorage.setItem("lastCity", cityInputValue); // Guarda el valor en localStorage.
      });
    </script>
</body>
</html>

<?php
// Incluye el archivo de funciones comunes.
require_once 'utils.php';

$apiKey = ''; // Define tu clave API aquí.

// Verifica que se hayan recibido las coordenadas (latitud y longitud) por GET.
if (!isset($_GET['lat']) || !isset($_GET['lon'])) { // Comprueba si los parámetros existen.
    die("<p class='error'>No se proporcionaron las coordenadas.</p>"); // Muestra un mensaje de error y para la ejecución.
}

$lat = $_GET['lat']; // Asigna la latitud recibida.
$lon = $_GET['lon']; // Asigna la longitud recibida.

// Obtiene el pronóstico utilizando las coordenadas.
$forecast = getForecast($lat, $lon, $apiKey);
if (!$forecast) { // Comprueba si se obtuvieron datos.
    die("<p class='error'>Error al obtener la previsión por horas.</p>"); // Muestra error y detiene la ejecución.
}

// Inicializa arrays para almacenar datos del gráfico y códigos de iconos.
$labels = [];      // Almacena las etiquetas (horario).
$temps = [];       // Almacena las temperaturas.
$rainValues = [];  // Almacena los valores de lluvia.
$icons = [];       // Almacena el código del icono del clima.

foreach ($forecast['list'] as $item) { // Recorre todos los registros del pronóstico.
    $labels[] = date('H:i', $item['dt']); // Convierte el timestamp a formato "Hora:Minutos" y lo añade.
    $temps[] = $item['main']['temp']; // Añade la temperatura al array.
    $rainValues[] = isset($item['rain']['3h']) ? $item['rain']['3h'] : 0; // Añade la lluvia (si existe) o 0.
    $icons[] = $item['weather'][0]['icon']; // Obtiene y guarda el código del icono del clima.
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Previsión por Horas</title>
    <!-- Enlaza la hoja de estilos -->
    <link rel="stylesheet" href="css/styles.css">
    <!-- Incluye Chart.js para la gráfica -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Estilos específicos para el botón y la sección de iconos en la página de previsión por horas */
        .btn {
            display: inline-block; /* Hace que el botón se comporte como un bloque en línea */
            padding: 10px 20px; /* Espaciado interno */
            background-color: rgb(12, 107, 209); /* Color de fondo */
            color: white; /* Color de texto */
            text-align: center; /* Centra el texto */
            border-radius: 5px; /* Bordes redondeados */
            text-decoration: none; /* Sin subrayado en enlaces */
            font-size: 16px; /* Tamaño del texto */
        }
        .btn:hover { /* Efecto al pasar el mouse */
            background-color: rgb(4, 82, 166); /* Nuevo color de fondo */
        }
        /* Estilos para el contenedor de iconos */
        #hourlyIcons {
            display: flex; /* Distribuye elementos en línea */
            flex-wrap: wrap; /* Permite ir a la siguiente línea si es necesario */
            justify-content: center; /* Centra los iconos horizontalmente */
            margin-top: 20px; /* Margin superior */
        }
        #hourlyIcons img {
            width: 50px; /* Ancho de las imágenes */
            height: 50px; /* Alto de las imágenes */
            margin: 5px; /* Espaciado entre iconos */
        }
    </style>
</head>
<body>
    <h1>Previsión por Horas</h1>
    <div class="forecast-container"> <!-- Contenedor para la previsión -->
        <h2>Temperaturas y Lluvia por Horas</h2>
        <canvas id="hourlyChart"></canvas> <!-- Área para el gráfico -->
        <div id="hourlyIcons"> <!-- Contenedor para mostrar los iconos -->
            <?php
            // Recorre el array de iconos y muestra cada imagen usando la URL correspondiente.
            foreach ($icons as $icon) {
                echo "<img src='http://openweathermap.org/img/wn/{$icon}@2x.png' alt='Icono del clima'>";
            }
            ?>
        </div>
    </div>
    <a href="index.php" class="btn">Inicio</a> <!-- Botón para regresar a la página principal -->
    <script>
        // Convierte los arrays PHP a variables JavaScript utilizando json_encode.
        const labels = <?php echo json_encode($labels); ?>;
        const temps = <?php echo json_encode($temps); ?>;
        const rainValues = <?php echo json_encode($rainValues); ?>;
        const ctx = document.getElementById('hourlyChart').getContext('2d'); // Obtiene el contexto del canvas para Chart.js.
        
        // Crea un gráfico utilizando Chart.js.
        new Chart(ctx, {
            type: 'bar', // Tipo base de gráfico.
            data: {
                labels: labels, // Etiquetas del eje X.
                datasets: [
                    {
                        label: 'Temperatura (°C)', // Leyenda del dataset.
                        type: 'line', // Muestra datos de temperatura como línea.
                        data: temps, // Datos de temperatura.
                        backgroundColor: 'rgba(70, 151, 205, 0.2)', // Color de fondo de la línea.
                        borderColor: 'rgb(96, 166, 213)', // Color del borde de la línea.
                        borderWidth: 2, // Grosor del borde.
                        fill: true, // Rellena el área debajo de la línea.
                        yAxisID: 'y' // Asociado al eje Y principal.
                    },
                    {
                        label: 'Lluvia (mm)', // Leyenda para datos de lluvia.
                        type: 'bar', // Muestra datos de lluvia en forma de barras.
                        data: rainValues, // Datos de lluvia.
                        backgroundColor: 'rgba(58, 168, 168, 0.5)', // Color de fondo de las barras.
                        borderColor: 'rgb(46, 185, 185)', // Color del borde de las barras.
                        borderWidth: 1, // Grosor del borde.
                        yAxisID: 'y1' // Asociado a un eje Y secundario.
                    }
                ]
            },
            options: {
                responsive: true, // Hace el gráfico adaptable a diferentes tamaños.
                plugins: {
                    title: { display: true, text: 'Temperaturas y Lluvia por Horas' } // Título del gráfico.
                },
                scales: {
                    x: { title: { display: true, text: 'Hora' } }, // Configuración del eje X.
                    y: { title: { display: true, text: 'Temperatura (°C)' }, position: 'left' }, // Configuración del eje Y principal.
                    y1: { // Configuración del eje Y secundario.
                        title: { display: true, text: 'Lluvia (mm)' },
                        position: 'right',
                        grid: { drawOnChartArea: false } // Evita que la cuadrícula del eje secundario se dibuje sobre el gráfico.
                    }
                }
            }
        });
    </script>
</body>
</html>

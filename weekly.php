
<?php
// Incluye el archivo de funciones comunes.
require_once 'utils.php';

$apiKey = ''; // Define tu clave API.

// Comprueba que se hayan recibido las coordenadas a través de GET.
if (!isset($_GET['lat']) || !isset($_GET['lon'])) { // Verifica la existencia de latitud y longitud.
    die("<p class='error'>No se proporcionaron las coordenadas.</p>"); // Muestra un error y termina la ejecución si faltan parámetros.
}

$lat = $_GET['lat']; // Asigna la latitud.
$lon = $_GET['lon']; // Asigna la longitud.

// Obtiene el pronóstico del clima utilizando la función getForecast.
$forecast = getForecast($lat, $lon, $apiKey);
if (!$forecast) { // Si no se obtienen datos.
    die("<p class='error'>Error al obtener la previsión semanal.</p>"); // Muestra un error y detiene la ejecución.
}

// Agrupa los datos por día.
$dailyData = [];
foreach ($forecast['list'] as $item) { // Recorre cada registro del pronóstico.
    $date = date('Y-m-d', $item['dt']); // Convierte el timestamp a una fecha (YYYY-MM-DD).
    if (!isset($dailyData[$date])) { // Si aún no se ha creado un registro para este día,
        $dailyData[$date] = [
            'min_temp' => $item['main']['temp_min'], // Establece la temperatura mínima del día.
            'max_temp' => $item['main']['temp_max'], // Establece la temperatura máxima del día.
            'rain' => isset($item['rain']['3h']) ? $item['rain']['3h'] : 0, // Establece la lluvia (o 0 si no hay datos).
            'date' => $date, // Guarda la fecha.
            'icon' => $item['weather'][0]['icon'] // Guarda el código del icono representativo.
        ];
    } else { // Si ya existe un registro para este día, actualiza los datos.
        if ($item['main']['temp_min'] < $dailyData[$date]['min_temp']) { // Comprueba si la nueva temperatura mínima es menor.
            $dailyData[$date]['min_temp'] = $item['main']['temp_min']; // Actualiza la temperatura mínima.
        }
        if ($item['main']['temp_max'] > $dailyData[$date]['max_temp']) { // Comprueba si la nueva máxima es mayor.
            $dailyData[$date]['max_temp'] = $item['main']['temp_max']; // Actualiza la temperatura máxima.
        }
        if (isset($item['rain']['3h'])) { // Si existe información de lluvia,
            $dailyData[$date]['rain'] += $item['rain']['3h']; // Suma la lluvia a la acumulada del día.
        }
    }
}

// Prepara arrays para etiquetas, temperaturas, lluvia e iconos.
$labels = []; // Almacena las etiquetas (fecha formateada).
$temps = [];  // Almacena arrays con temperaturas mínimas y máximas.
$rain = [];   // Almacena los valores de lluvia diarios.
$icons = [];  // Almacena el código de icono representativo de cada día.

foreach ($dailyData as $day) { // Recorre cada día con datos.
    $labels[] = date('D, M j', strtotime($day['date'])); // Formatea y guarda la fecha (por ejemplo, "Mon, Mar 11").
    $temps[] = [ // Guarda las temperaturas mínima y máxima en un array.
        'min' => $day['min_temp'],
        'max' => $day['max_temp']
    ];
    $rain[] = $day['rain']; // Guarda el total de lluvia del día.
    $icons[] = $day['icon']; // Guarda el código del icono representativo.
}
?>
<!DOCTYPE html> 
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Previsión Semanal</title>
    <!-- Enlaza el archivo de estilos -->
    <link rel="stylesheet" href="css/styles.css">
    <!-- Incluye Chart.js para generar el gráfico -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Estilos para el botón y para el contenedor de iconos en la página semanal */
        .btn {
            display: inline-block; /* Hace el botón en línea */
            padding: 10px 20px; /* Espaciado interno */
            background-color: rgb(12, 107, 209); /* Color de fondo */
            color: white; /* Color del texto */
            text-align: center; /* Centra el texto */
            border-radius: 5px; /* Bordes redondeados */
            text-decoration: none; /* Sin subrayado */
            font-size: 16px; /* Tamaño del texto */
        }
        .btn:hover { /* Efecto al pasar el mouse */
            background-color: rgb(4, 82, 166); /* Nuevo color de fondo */
        }
        /* Estilos para el contenedor de iconos representativos */
        #weeklyIcons {
            display: flex; /* Muestra los iconos en línea */
            justify-content: center; /* Centra horizontalmente */
            flex-wrap: wrap; /* Permite saltos de línea si es necesario */
            margin-top: 20px; /* Espacio superior */
        }
        #weeklyIcons img {
            width: 50px; /* Ancho de los iconos */
            height: 50px; /* Alto de los iconos */
            margin: 5px; /* Espaciado entre iconos */
        }
    </style>
</head>
<body>
    <h1>Previsión Semanal</h1>
    <div class="forecast-container"> <!-- Contenedor principal -->
        <h2>Temperaturas y Lluvia Semanal</h2>
        <canvas id="weeklyChart"></canvas> <!-- Área del gráfico -->
        <div id="weeklyIcons"> <!-- Contenedor para iconos representativos de cada día -->
            <?php
            // Recorre el array de iconos y muestra cada imagen.
            foreach ($icons as $icon) {
                echo "<img src='http://openweathermap.org/img/wn/{$icon}@2x.png' alt='Icono del clima'>";
            }
            ?>
        </div>
    </div>
    <a href="index.php" class="btn">Inicio</a> <!-- Botón para regresar a la página principal -->
    <script>
        // Convierte los arrays PHP a variables JavaScript.
        const labels = <?php echo json_encode($labels); ?>;
        const temps = <?php echo json_encode($temps); ?>;
        const rain = <?php echo json_encode($rain); ?>;
        const ctx = document.getElementById('weeklyChart').getContext('2d'); // Obtiene el contexto del canvas.
        
        // Crea el gráfico combinando barras y línea usando Chart.js.
        new Chart(ctx, {
            type: 'bar', // Tipo base de gráfico.
            data: {
                labels: labels, // Etiquetas del eje X.
                datasets: [
                    {
                        label: 'Temperatura Mínima (°C)', // Leyenda para la temperatura mínima.
                        data: temps.map(t => t.min), // Extrae las temperaturas mínimas.
                        backgroundColor: 'rgba(44, 137, 199, 0.2)', // Color de fondo de las barras.
                        borderColor: 'rgb(67, 158, 218)', // Color del borde de las barras.
                        borderWidth: 1 // Grosor del borde.
                    },
                    {
                        label: 'Temperatura Máxima (°C)', // Leyenda para la temperatura máxima.
                        data: temps.map(t => t.max), // Extrae las temperaturas máximas.
                        backgroundColor: 'rgba(231, 117, 91, 0.18)', // Color de fondo de las barras.
                        borderColor: 'rgb(224, 53, 90)', // Color del borde de las barras.
                        borderWidth: 1 // Grosor del borde.
                    },
                    {
                        label: 'Lluvia (mm)', // Leyenda para la lluvia.
                        data: rain, // Datos de lluvia.
                        backgroundColor: 'rgba(27, 118, 118, 0.2)', // Color de fondo para la línea.
                        borderColor: 'rgba(75, 192, 192, 1)', // Color de la línea.
                        borderWidth: 1, // Grosor de la línea.
                        type: 'line', // Muestra los datos de lluvia como una línea.
                        yAxisID: 'rainAxis' // Asocia a un eje Y secundario.
                    }
                ]
            },
            options: {
                responsive: true, // Hace el gráfico adaptable.
                plugins: {
                    title: { display: true, text: 'Temperaturas y Lluvia Semanal' } // Define el título del gráfico.
                },
                scales: {
                    x: { title: { display: true, text: 'Día' } }, // Ajusta el título del eje X.
                    y: { title: { display: true, text: 'Temperatura (°C)' } }, // Ajusta el título del eje Y principal.
                    rainAxis: { // Configuración para el eje secundario.
                        position: 'right', // Ubicación en el lado derecho.
                        title: { display: true, text: 'Lluvia (mm)' }, // Título del eje secundario.
                        grid: { display: false } // Oculta la cuadrícula para este eje.
                    }
                }
            }
        });
    </script>
</body>
</html>

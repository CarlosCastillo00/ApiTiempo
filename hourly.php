<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Definir la codificación de caracteres como UTF-8 -->
    <meta charset="UTF-8">
    <!-- Título de la página que se muestra en la pestaña del navegador -->
    <title>Previsión por Horas</title>
    <!-- Vincular el archivo de estilos CSS para el diseño de la página -->
    <link rel="stylesheet" href="css/styles.css">
    <!-- Cargar la librería Chart.js para generar gráficos -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Estilo para el botón */
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color:rgb(12, 107, 209);
            color: white;
            text-align: center;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
        }

        /* Estilo para el botón cuando se pasa el mouse por encima */
        .btn:hover {
            background-color:rgb(4, 82, 166);
        }
    </style>
</head>
<body>
    <!-- Encabezado principal de la página -->
    <h1>Previsión por Horas</h1>

    <div class="forecast-container">
        <!-- Subtítulo para el gráfico de temperaturas y lluvia -->
        <h2>Temperaturas y Lluvia por Horas</h2>
        <!-- El lienzo donde se dibujará el gráfico -->
        <canvas id="hourlyChart"></canvas>
    </div>

    <!-- Botón para ir a la página principal de clima -->
    <a href="index.php" class="btn">Inicio</a>

    <?php
    // Definir la clave API de OpenWeatherMap (reemplazar con tu propia clave)
    $apiKey = '';
    // Obtener las coordenadas latitud y longitud desde los parámetros GET
    $lat = $_GET['lat'];
    $lon = $_GET['lon'];

    // URL para obtener la previsión por horas usando las coordenadas obtenidas
    $hourlyUrl = "https://api.openweathermap.org/data/2.5/forecast?lat={$lat}&lon={$lon}&appid={$apiKey}&units=metric&lang=es";
    // Hacer una solicitud para obtener los datos de la previsión por horas
    $hourlyResponse = @file_get_contents($hourlyUrl);

    // Comprobar si hubo un error al obtener los datos
    if ($hourlyResponse === FALSE) {
        echo "<p class='error'>Error al obtener la previsión por horas.</p>";
        exit; // Detener el script si hay error
    }

    // Decodificar la respuesta JSON
    $hourlyData = json_decode($hourlyResponse, true);

    // Preparar arrays para almacenar los datos del gráfico
    $labels = []; // Etiquetas para las horas
    $temperatures = []; // Temperaturas por hora
    $rainfall = []; // Lluvia por hora

    // Recorrer los datos de la previsión por horas
    foreach ($hourlyData['list'] as $hour) {
        // Obtener la hora en formato HH:MM
        $labels[] = date('H:i', $hour['dt']);
        // Obtener la temperatura en grados Celsius
        $temperatures[] = $hour['main']['temp'];
        // Obtener la cantidad de lluvia en mm (0 si no hay lluvia)
        $rainfall[] = isset($hour['rain']['3h']) ? $hour['rain']['3h'] : 0;
    }
    ?>

    <script>
        // Pasar los datos de PHP a JavaScript para el gráfico
        const labels = <?php echo json_encode($labels); ?>;
        const temperatures = <?php echo json_encode($temperatures); ?>;
        const rainfall = <?php echo json_encode($rainfall); ?>;

        // Configuración del gráfico
        const ctx = document.getElementById('hourlyChart').getContext('2d');
        const hourlyChart = new Chart(ctx, {
            type: 'bar', // Tipo de gráfico principal (barra)
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Temperatura (°C)', // Dataset para la temperatura
                        type: 'line', // Tipo de gráfico para la temperatura (línea)
                        data: temperatures,
                        backgroundColor: 'rgba(70, 151, 205, 0.2)',
                        borderColor: 'rgb(96, 166, 213)',
                        borderWidth: 2,
                        fill: true,
                        yAxisID: 'y', // Eje y para las temperaturas
                    },
                    {
                        label: 'Lluvia (mm)', // Dataset para la lluvia
                        type: 'bar', // Tipo de gráfico para la lluvia (barra)
                        data: rainfall,
                        backgroundColor: 'rgba(58, 168, 168, 0.5)',
                        borderColor: 'rgb(46, 185, 185)',
                        borderWidth: 1,
                        yAxisID: 'y1', // Eje y1 para la lluvia
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Temperaturas y Lluvia por Horas'
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Hora' // Título para el eje X (Hora)
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Temperatura (°C)' // Título para el eje Y (Temperatura)
                        },
                        position: 'left', // Eje izquierdo para la temperatura
                    },
                    y1: {
                        title: {
                            display: true,
                            text: 'Lluvia (mm)' // Título para el eje Y1 (Lluvia)
                        },
                        position: 'right', // Eje derecho para la lluvia
                        grid: {
                            drawOnChartArea: false // Desactiva la cuadrícula en el eje de la lluvia
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>

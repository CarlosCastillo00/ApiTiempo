<!DOCTYPE html> 
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Previsión Semanal</title>
    <link rel="stylesheet" href="css/styles.css"> <!-- Enlace a la hoja de estilos externa -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Carga de la librería Chart.js -->
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
    <h1>Previsión Semanal</h1>
    <div class="forecast-container">
        <h2>Temperaturas y Lluvia Semanal</h2>
        <canvas id="weeklyChart"></canvas> <!-- Canvas donde se renderizará el gráfico -->
        <div id="weatherIcons">
            <!-- Aquí se mostrarán los iconos del clima -->
        </div>
    </div>
      <!-- Botón para ir a la página principal de clima -->
      <a href="index.php" class="btn">Inicio</a>

    <?php
    // Clave de API para obtener los datos del clima
    $apiKey = ''; 
    
    // Obtener la latitud y longitud de la URL
    $lat = $_GET['lat'];
    $lon = $_GET['lon'];

    // Construcción de la URL de la API de OpenWeatherMap
    $url = "https://api.openweathermap.org/data/2.5/forecast?lat={$lat}&lon={$lon}&appid={$apiKey}&units=metric&lang=es";
    $response = @file_get_contents($url); // Realiza la petición a la API

    // Verifica si hubo un error en la petición
    if ($response === FALSE) {
        echo "<p class='error'>Error al obtener la previsión semanal.</p>";
        exit;
    }

    // Decodifica la respuesta JSON en un array de PHP
    $data = json_decode($response, true);

    // Agrupación de datos por día
    $dailyData = [];
    foreach ($data['list'] as $forecast) {
        $date = date('Y-m-d', $forecast['dt']); // Extrae la fecha de cada registro
        
        // Si el día aún no ha sido registrado, inicializa los valores
        if (!isset($dailyData[$date])) {
            $dailyData[$date] = [
                'min_temp' => $forecast['main']['temp_min'],
                'max_temp' => $forecast['main']['temp_max'],
                'rain' => $forecast['rain']['3h'] ?? 0, // Precipitación acumulada en mm
                'icon' => $forecast['weather'][0]['icon'], // Icono del clima
                'date' => $date
            ];
        } else {
            // Actualiza la temperatura mínima y máxima
            if ($forecast['main']['temp_min'] < $dailyData[$date]['min_temp']) {
                $dailyData[$date]['min_temp'] = $forecast['main']['temp_min'];
            }
            if ($forecast['main']['temp_max'] > $dailyData[$date]['max_temp']) {
                $dailyData[$date]['max_temp'] = $forecast['main']['temp_max'];
            }
            // Suma la precipitación si hay datos
            if (isset($forecast['rain']['3h'])) {
                $dailyData[$date]['rain'] += $forecast['rain']['3h'];
            }
        }
    }

    // Preparación de los datos para JavaScript
    $labels = []; // Días de la semana
    $temperatures = []; // Temperaturas mínimas y máximas
    $rain = []; // Precipitación en mm
    $icons = []; // Iconos del clima

    foreach ($dailyData as $day) {
        $labels[] = date('D, M j', strtotime($day['date'])); // Formato de fecha
        $temperatures[] = [
            'min' => $day['min_temp'],
            'max' => $day['max_temp']
        ];
        $rain[] = $day['rain'];
        $icons[] = $day['icon'];
    }
    ?>

    <script>
        // Datos importados desde PHP a JavaScript
        const labels = <?php echo json_encode($labels); ?>;
        const temperatures = <?php echo json_encode($temperatures); ?>;
        const rain = <?php echo json_encode($rain); ?>;
        const icons = <?php echo json_encode($icons); ?>;

        // Configuración del gráfico con Chart.js
        const ctx = document.getElementById('weeklyChart').getContext('2d');
        const weeklyChart = new Chart(ctx, {
            type: 'bar', // Tipo de gráfico: barras
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Temperatura Mínima (°C)',
                        data: temperatures.map(temp => temp.min),
                        backgroundColor: 'rgba(44, 137, 199, 0.2)',
                        borderColor: 'rgb(67, 158, 218)',
                        borderWidth: 1,
                    },
                    {
                        label: 'Temperatura Máxima (°C)',
                        data: temperatures.map(temp => temp.max),
                        backgroundColor: 'rgba(231, 117, 91, 0.18)',
                        borderColor: 'rgb(224, 53, 90)',
                        borderWidth: 1,
                    },
                    {
                        label: 'Lluvia (mm)',
                        data: rain,
                        backgroundColor: 'rgba(27, 118, 118, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1,
                        type: 'line', // Se representa como línea
                        yAxisID: 'rainAxis',
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Temperaturas y Lluvia Semanal'
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Día'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Temperatura (°C)'
                        }
                    },
                    rainAxis: {
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Lluvia (mm)'
                        },
                        grid: {
                            display: false,
                        }
                    }
                }
            }
        });
    </script>

    <style>
        /* Estilos para los iconos del clima */
        #weatherIcons {
            display: flex;
            justify-content: space-around;
            margin-top: 20px;
        }
        .weather-icon {
            text-align: center;
        }
        .weather-icon img {
            width: 50px;
            height: 50px;
        }
    </style>
</body>
</html>

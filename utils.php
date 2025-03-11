<?php
// Función para obtener las coordenadas y nombre estandarizado de una ciudad usando la API de OpenWeatherMap.
function getCityCoordinates($city, $apiKey) { // Recibe el nombre de la ciudad y la clave de API.
    $cityEncoded = urlencode($city); // Codifica el nombre de la ciudad para que sea seguro en la URL.
    // Construye la URL para obtener las coordenadas mediante la API de geolocalización.
    $url = "http://api.openweathermap.org/geo/1.0/direct?q={$cityEncoded}&limit=1&appid={$apiKey}";
    $response = @file_get_contents($url); // Realiza la solicitud a la API y suprime mensajes de error.
    if ($response === FALSE) { // Comprueba si la respuesta es incorrecta.
        return false; // Retorna false en caso de error.
    }
    $data = json_decode($response, true); // Convierte la respuesta JSON a un array asociativo.
    if (empty($data)) { // Verifica si se obtuvieron datos vacíos.
        return false; // Si no hay datos, retorna false.
    }
    // Retorna un array con la latitud, longitud y nombre de la ciudad.
    return [
        'lat' => $data[0]['lat'], // Latitud.
        'lon' => $data[0]['lon'], // Longitud.
        'name' => $data[0]['name'] // Nombre de la ciudad.
    ];
}

// Función para obtener el clima actual usando las coordenadas.
function getCurrentWeather($lat, $lon, $apiKey) { // Recibe latitud, longitud y clave de API.
    // Construye la URL para obtener el clima actual en unidades métricas y en español.
    $url = "https://api.openweathermap.org/data/2.5/weather?lat={$lat}&lon={$lon}&appid={$apiKey}&units=metric&lang=es";
    $response = @file_get_contents($url); // Realiza la solicitud a la API.
    if ($response === FALSE) { // Comprueba si ocurrió un error en la solicitud.
        return false; // Retorna false en caso de error.
    }
    return json_decode($response, true); // Decodifica la respuesta JSON y retorna el array de datos.
}

// Función para obtener el pronóstico (forecast) usando las mismas coordenadas.
function getForecast($lat, $lon, $apiKey) { // Recibe latitud, longitud y clave de API.
    // Construye la URL para obtener el pronóstico en unidades métricas y en español.
    $url = "https://api.openweathermap.org/data/2.5/forecast?lat={$lat}&lon={$lon}&appid={$apiKey}&units=metric&lang=es";
    $response = @file_get_contents($url); // Realiza la solicitud a la API.
    if ($response === FALSE) { // Comprueba si la solicitud falla.
        return false; // Retorna false ante error.
    }
    return json_decode($response, true); // Decodifica y retorna el array de datos del pronóstico.
}
?>

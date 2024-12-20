<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Predicción de Demanda de Taxis</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f3f2ef;
        }
        .linkedin-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .linkedin-header {
            background-color: #1d4ed8;
            color: white;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto max-w-4xl px-4 py-8">
        <div class="linkedin-card">
            <div class="linkedin-header p-4 rounded-t-lg flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center">
                        <span class="text-3xl">🚕</span>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold">Predicción de Demanda de Taxis</h1>
                        <p class="text-sm">Análisis de Zonas de Mayor Tráfico</p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <!-- Prediction Form -->
                <form method="post" action="" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-1 gap-4">
                        <div>
                            <label for="PULocationID" class="block text-sm font-medium text-gray-700">PULocationID</label>
                            <input type="number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" id="PULocationID" name="PULocationID" required>
                        </div>
                    </div>
                    <div>
                        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition duration-300">
                            <i class="fas fa-chart-line mr-2"></i>Zona con Mayor Probabilidad de Viajes
                        </button>
                    </div>
                </form>

                <!-- Prediction Results -->
                <?php
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    // Obtener los datos del formulario
                    $PULocationID = $_POST['PULocationID'];
                    $pickup_weekday = date('w'); // Día de la semana (0 para Domingo hasta 6 para Sábado)
                    $pickup_hour = date('G');    // Hora actual en formato 24 horas
                    
                    // URL del endpoint de FastAPI
                    $url = 'http://100.78.115.22:9000/predict/';

                    // Datos a enviar en la solicitud
                    $data = array(
                        "PULocationID" => $PULocationID,
                        "pickup_weekday" => $pickup_weekday,
                        "pickup_hour" => $pickup_hour
                    );

                    // Inicializar cURL
                    $ch = curl_init($url);

                    // Configurar las opciones de cURL
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

                    // Ejecutar la solicitud y obtener la respuesta
                    $response = curl_exec($ch);

                    if (curl_errno($ch)) {
                        echo '<div class="mt-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded relative" role="alert">';
                        echo 'Error: ' . curl_error($ch);
                        echo '</div>';
                    } else {
                        $data = json_decode($response, true);
                        if (isset($data['max_zone_name'])) {
                            echo '<div class="mt-4 bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded relative" role="alert">';
                            echo '<strong class="font-bold">Resultado: </strong>';
                            echo 'La zona con mayor probabilidad de tráfico es: ' . $data['max_zone_name'] . ' a las ' . date('G:i');
                            echo '</div>';
                        } else {
                            echo '<div class="mt-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded relative" role="alert">';
                            echo 'Error en la predicción: ' . json_encode($data);
                            echo '</div>';
                        }
                    }

                    // Cerrar cURL
                    curl_close($ch);
                }
                ?>
            </div>
        </div>
    </div>
</body>
</html>

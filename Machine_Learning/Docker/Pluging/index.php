<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Predicción de Demanda de Taxis</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        .gradient-header {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        }
        .input-field {
            transition: all 0.3s ease;
        }
        .input-field:focus {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
            border-color: #3b82f6;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="container max-w-xl w-full">
        <div class="glass-card overflow-hidden">
            <div class="gradient-header p-6 flex items-center space-x-4">
                <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center shadow-lg">
                    <span class="text-4xl">🚕</span>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-white">Predicción de Demanda de Taxis</h1>
                    <p class="text-sm text-blue-100">Análisis Inteligente de Zonas de Tráfico</p>
                </div>
            </div>

            <div class="p-8">
                <form method="post" action="" class="space-y-6">
                    <div class="grid md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">ID de la Zona</label>
                            <input type="number" name="zone_name" class="input-field w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none" placeholder="Ingrese ID" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Día de la Semana</label>
                            <select name="pickup_weekday" class="input-field w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none" required>
                                <option value="">Seleccione un día</option>
                                <option value="0">Lunes</option>
                                <option value="1">Martes</option>
                                <option value="2">Miércoles</option>
                                <option value="3">Jueves</option>
                                <option value="4">Viernes</option>
                                <option value="5">Sábado</option>
                                <option value="6">Domingo</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Hora de Recogida</label>
                            <input type="number" name="pickup_hour" class="input-field w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none" min="0" max="23" placeholder="0-23" required>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition duration-300 flex items-center justify-center space-x-2">
                        <i class="fas fa-chart-line"></i>
                        <span>Predecir Zona de Mayor Demanda</span>
                    </button>
                </form>

                <!-- PHP Prediction Results -->
                <?php
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    $zone_name = $_POST['zone_name'];
                    $pickup_weekday = $_POST['pickup_weekday'];
                    $pickup_hour = $_POST['pickup_hour'];

                    $url = 'http://100.78.115.22:9000/predict/';
                    $data = array(
                        "PULocationID" => $zone_name,
                        "pickup_weekday" => $pickup_weekday,
                        "pickup_hour" => $pickup_hour
                    );

                    $ch = curl_init($url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                    $response = curl_exec($ch);

                    if (curl_errno($ch)) {
                        echo '<div class="mt-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg flex items-center space-x-3">
                                <i class="fas fa-exclamation-triangle text-red-600"></i>
                                <p class="text-red-800 font-medium">Error de Conexión: ' . curl_error($ch) . '</p>
                              </div>';
                    } else {
                        $data = json_decode($response, true);
                        
                        // Main prediction result
                        if (isset($data['max_zone_name'])) {
                            echo '<div class="mt-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg">
                                    <div class="flex items-center space-x-3">
                                        <i class="fas fa-map-marker-alt text-green-600"></i>
                                        <h3 class="text-lg font-semibold text-green-800">Resultado de la Predicción</h3>
                                    </div>
                                    <p class="mt-2 text-green-700">La zona con mayor probabilidad de tráfico es: <strong>' . htmlspecialchars($data['max_zone_name']) . '</strong></p>
                                  </div>';
                        }

                        // Full API response
                        echo '<div class="mt-4 bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg">
                                <div class="flex items-center space-x-3">
                                    <i class="fas fa-info-circle text-blue-600"></i>
                                    <h3 class="text-lg font-semibold text-blue-800">Respuesta Completa de la API</h3>
                                </div>
                                <pre class="mt-2 text-sm text-blue-700 overflow-x-auto">' . 
                                    htmlspecialchars(json_encode(json_decode($response), JSON_PRETTY_PRINT)) . 
                                '</pre>
                              </div>';
                    }

                    curl_close($ch);
                }
                ?>
            </div>
        </div>
    </div>
</body>
</html>
<?php
$hostname = "localhost";
$username = "root";
$password = "";
$database = "sensor_db";

// Connect to MySQL database
$conn = mysqli_connect($hostname, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Function to classify temperature based on predefined ranges
function classifyTemperature($temp) {
    if ($temp < 10) {
        return '<span style="color: blue;">Cold</span>';
    } else if ($temp >= 10 && $temp < 25) {
        return '<span style="color: green;">Comfortable</span>';
    } else if ($temp >= 25 && $temp < 30) {
        return '<span style="color: orange;">Warm</span>';
    } else {
        return '<span style="color: red;">Hot</span>';
    }
}

// Function to classify humidity based on predefined ranges
function classifyHumidity($humidity) {
    if ($humidity < 30) {
        return '<span style="color: blue;">Dry</span>';
    } else if ($humidity >= 30 && $humidity < 60) {
        return '<span style="color: green;">Comfortable</span>';
    } else {
        return '<span style="color: red;">Humid</span>';
    }
}

// Function to classify LDR value based on predefined ranges
function classifyLDR($ldr) {
    if ($ldr < 200) {
        return '<span style="color: green;">Bright Light</span>';
    } else if ($ldr >= 200 && $ldr < 600) {
        return '<span style="color: orange;">Medium Light</span>';
    } else {
        return '<span style="color: red;">Low Light</span>';
    }
}

// Function to generate combined notification based on the sensor data
function getNotification($t, $h, $ldr) {
    // Classify temperature
    if ($t < 10) {
        $tempClass = "Cold";
    } else if ($t >= 10 && $t < 25) {
        $tempClass = "Comfortable";
    } else if ($t >= 25 && $t< 30) {
        $tempClass = "Warm";
    } else {
        $tempClass = "Hot";
    }

    // Classify humidity
    if ($h < 30) {
        $humidityClass = "Dry";
    } else if ($h >= 30 && $h < 60) {
        $humidityClass = "Comfortable";
    } else {
        $humidityClass = "Humid";
    }

    // Classify LDR value
    if ($ldr < 200) {
        $ldrClass = "Bright Light";
    } else if ($ldr >= 200 && $ldr < 600) {
        $ldrClass = "Medium Light";
    } else {
        $ldrClass = "Low Light";
    }
	

    
    // Determine notification based on combined classification
    if ($tempClass == "Hot" && $humidityClass == "Humid" && $ldrClass == "Bright Light") {
        return "It's a bright and sunny day. You should turn on the fan to cool down.";
    } elseif ($tempClass == "Hot" && $humidityClass == "Humid" && $ldrClass == "Low Light") {
        return "It's hot and humid with low light. Ensure proper ventilation.";
    } elseif ($tempClass == "Hot" && $humidityClass == "Dry" && $ldrClass == "Bright Light") {
        return "It's hot and dry with bright light. Stay hydrated and use sunscreen.";
    } elseif ($tempClass == "Hot" && $humidityClass == "Dry" && $ldrClass == "Low Light") {
        return "It's hot and dry with low light. Stay indoors and stay hydrated.";
    } elseif ($tempClass == "Warm" && $humidityClass == "Humid" && $ldrClass == "Bright Light") {
        return "It's warm and humid with bright light. Enjoy the day with proper ventilation.";
    } elseif ($tempClass == "Warm" && $humidityClass == "Humid" && $ldrClass == "Low Light") {
        return "It's warm and humid with low light. Ensure proper ventilation and stay comfortable.";
    } elseif ($tempClass == "Warm" && $humidityClass == "Dry" && $ldrClass == "Bright Light") {
        return "It's warm and dry with bright light. Stay hydrated and enjoy the warmth.";
    } elseif ($tempClass == "Warm" && $humidityClass == "Dry" && $ldrClass == "Low Light") {
        return "It's warm and dry with low light. Enjoy a comfortable indoor day.";
    } elseif ($tempClass == "Cold" && $humidityClass == "Humid" && $ldrClass == "Bright Light") {
        return "It's cold and humid with bright light. Enjoy the sunlight but stay warm.";
    } elseif ($tempClass == "Cold" && $humidityClass == "Humid" && $ldrClass == "Low Light") {
        return "It's cold and humid with low light. Keep warm and stay inside if possible.";
    } elseif ($tempClass == "Cold" && $humidityClass == "Dry" && $ldrClass == "Bright Light") {
        return "It's cold and dry with bright light. Dress warmly and enjoy the sunlight.";
    } elseif ($tempClass == "Cold" && $humidityClass == "Dry" && $ldrClass == "Low Light") {
        return "It's cold and dry with low light. Stay warm indoors.";
    } elseif ($tempClass == "Comfortable" && $humidityClass == "Humid" && $ldrClass == "Bright Light") {
        return "It's a comfortable and bright day. Enjoy your time outside.";
    } elseif ($tempClass == "Comfortable" && $humidityClass == "Humid" && $ldrClass == "Low Light") {
        return "It's comfortable but with low light. You might need artificial lighting.";
    } elseif ($tempClass == "Comfortable" && $humidityClass == "Dry" && $ldrClass == "Bright Light") {
        return "It's comfortable and dry with bright light. Have a pleasant day.";
    } elseif ($tempClass == "Comfortable" && $humidityClass == "Dry" && $ldrClass == "Low Light") {
        return "It's comfortable and dry with low light. A relaxing day ahead.";
    } else {
        return "All systems are functioning normally.";
    }
}


// Check if POST data is received
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["temperature"]) && isset($_POST["humidity"]) && isset($_POST["ldrValue"])) {
        $t = $_POST["temperature"];
        $h = $_POST["humidity"];
        $ldr = $_POST["ldrValue"];

        $sql = "INSERT INTO dht22 (temperature, humidity, ldr_value) VALUES ('$t', '$h', '$ldr')";

        if (mysqli_query($conn, $sql)) {
            echo "New record created successfully";
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
    } else {
        echo "Temperature, humidity, or LDR value not set";
    }
}

// Fetch data for analysis
$sql = "SELECT * FROM dht22 ORDER BY id DESC";
$result = mysqli_query($conn, $sql);

$data = [];
$notifications = [];

if (mysqli_num_rows($result) > 0) {
    $temperatures = [];
    $humidities = [];
    $ldrValues = [];

    

    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
        $temperatures[] = $row['temperature'];
        $humidities[] = $row['humidity'];
        $ldrValues[] = $row['ldr_value'];

      
    }

    echo "</table>";

    // Generate combined notification for the latest row
    $latestRow = $data[0]; // Assuming data is ordered by id DESC
    $latestNotification = getNotification($latestRow['temperature'], $latestRow['humidity'], $latestRow['ldr_value']);

    // Output the latest notification
    echo "<div class=\"notifications\">";
	echo "<h2>Notification:</h2>";
	echo $latestNotification;
	echo "</div>";

    
    // Calculate averages and min/max values
    $averageTemp = array_sum($temperatures) / count($temperatures);
    $averageHumidity = array_sum($humidities) / count($humidities);
    $averageLdr = array_sum($ldrValues) / count($ldrValues);
    $maxTemp = max($temperatures);
    $minTemp = min($temperatures);
    $maxHumidity = max($humidities);
    $minHumidity = min($humidities);
    $maxLdr = max($ldrValues);
    $minLdr = min($ldrValues);
} else {
    echo "No data available.";
}

// Close MySQL connection
mysqli_close($conn);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<script>
        function autoRefresh() {
            window.location = window.location.href;
        }
        setInterval('autoRefresh()', 10000);
    </script>
    <title>Harshini's Intelligent Environmental Sensor Data</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('evn.jpg');
            margin: 20px;
			
        }
        .container {
			max-width: 800px;
			margin: auto;
			background-color: rgba(255, 255, 255, 0.9); /* White color with 80% opacity */
			padding: 20px;
			box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
			border-radius: 5px;
		}	

        h1 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }
        table th {
            background-color: #f2f2f2;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            color: #666;
        }
        .analysis {
            margin-top: 20px;
            text-align: center;
			
        }
        .charts {
            margin-top: 20px;
            text-align: center;
        }
        .notifications {
            margin-top: 20px;
            padding: 10px;
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container">
        <h1>Harshini's Intelligent Environmental Sensor Data</h1>

        <?php if (!empty($notifications)): ?>
        <div class="notifications">
            <h2>Notifications</h2>
            <ul>
                <li><?php echo end($latestNotification); ?></li>
            </ul>
        </div>
        <?php endif; ?>

        <div class="analysis">
            <h2>Data Analysis and Classification</h2>
            <table>
                <tr>
                    <th>Measurement</th>
                    <th>Average Value</th>
                    <th>Classification</th>
                </tr>
                <tr>
                    <td>Temperature</td>
                    <td><?php echo isset($averageTemp) ? number_format($averageTemp, 2) . ' °C' : 'N/A'; ?></td>
                    <td><?php echo isset($averageTemp) ? classifyTemperature($averageTemp) : 'N/A'; ?></td>
                </tr>
                <tr>
                    <td>Humidity</td>
                    <td><?php echo isset($averageHumidity) ? number_format($averageHumidity, 2) . ' %' : 'N/A'; ?></td>
                    <td><?php echo isset($averageHumidity) ? classifyHumidity($averageHumidity) : 'N/A'; ?></td>
                </tr>
                <tr>
                    <td>LDR Value</td>
                    <td><?php echo isset($averageLdr) ? number_format($averageLdr, 2) : 'N/A'; ?></td>
                    <td><?php echo isset($averageLdr) ? classifyLDR($averageLdr) : 'N/A'; ?></td>
                </tr>
                <tr>
                    <td>Max Temperature</td>
                    <td><?php echo isset($maxTemp) ? $maxTemp . ' °C' : 'N/A'; ?></td>
                    <td><?php echo isset($maxTemp) ? classifyTemperature($maxTemp) : 'N/A'; ?></td>
                </tr>
                <tr>
                    <td>Min Temperature</td>
                    <td><?php echo isset($minTemp) ? $minTemp . ' °C' : 'N/A'; ?></td>
                    <td><?php echo isset($minTemp) ? classifyTemperature($minTemp) : 'N/A'; ?></td>
                </tr>
                <tr>
                    <td>Max Humidity</td>
                    <td><?php echo isset($maxHumidity) ? $maxHumidity . ' %' : 'N/A'; ?></td>
                    <td><?php echo isset($maxHumidity) ? classifyHumidity($maxHumidity) : 'N/A'; ?></td>
                </tr>
                <tr>
                    <td>Min Humidity</td>
                    <td><?php echo isset($minHumidity) ? $minHumidity . ' %' : 'N/A'; ?></td>
                    <td><?php echo isset($minHumidity) ? classifyHumidity($minHumidity) : 'N/A'; ?></td>
                </tr>
                <tr>
                    <td>Max LDR Value</td>
                    <td><?php echo isset($maxLdr) ? $maxLdr : 'N/A'; ?></td>
                    <td><?php echo isset($maxLdr) ? classifyLDR($maxLdr) : 'N/A'; ?></td>
                </tr>
                <tr>
                    <td>Min LDR Value</td>
                    <td><?php echo isset($minLdr) ? $minLdr : 'N/A'; ?></td>
                    <td><?php echo isset($minLdr) ? classifyLDR($minLdr) : 'N/A'; ?></td>
                </tr>
            </table>
        </div>

        <div class="charts">
            <h2>Data Visualization</h2>
            <canvas id="temperatureChart" width="400" height="200"></canvas>
            <canvas id="humidityChart" width="400" height="200"></canvas>
            <canvas id="ldrChart" width="400" height="200"></canvas>
        </div>

        <div class="sensor-readings">
            <h2>Sensor Readings</h2>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Timestamp</th>
                    <th>Temperature (°C)</th>
                    <th>Humidity (%)</th>
                    <th>LDR Value</th>
                </tr>
                <?php foreach ($data as $row): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['timestamp']; ?></td>
                    <td><?php echo $row['temperature']; ?></td>
                    <td><?php echo $row['humidity']; ?></td>
                    <td><?php echo $row['ldr_value']; ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>

    <script>
        // Function to create line chart
        function createChart(ctx, label, data, color, minRange, maxRange) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: <?php echo json_encode(array_column($data, 'timestamp')); ?>,
                    datasets: [{
                        label: label,
                        data: data,
                        backgroundColor: color,
                        borderColor: color,
                        fill: false
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        xAxes: [{
                            type: 'time',
                            time: {
                                unit: 'day'
                            },
                            ticks: {
                                autoSkip: true,
                                maxTicksLimit: 10
                            }
                        }],
                        yAxes: [{
                            ticks: {
                                beginAtZero: false,
                                min: minRange,   // Set the minimum range for y-axis
                                max: maxRange    // Set the maximum range for y-axis
                            }
                        }]
                    },
                    tooltips: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            title: function(tooltipItem, data) {
                                return 'Date: ' + tooltipItem[0].xLabel;
                            },
                            label: function(tooltipItem, data) {
                                return label + ': ' + tooltipItem.yLabel;
                            }
                        }
                    }
                }
            });
        }

        // Initialize charts
        document.addEventListener('DOMContentLoaded', function() {
            createChart(document.getElementById('temperatureChart').getContext('2d'), 'Temperature (°C)', <?php echo json_encode(array_column($data, 'temperature')); ?>, 'red', 0, 40); // Adjust the min and max range for temperature
            createChart(document.getElementById('humidityChart').getContext('2d'), 'Humidity (%)', <?php echo json_encode(array_column($data, 'humidity')); ?>, 'blue', 0, 100); // Adjust the min and max range for humidity
            createChart(document.getElementById('ldrChart').getContext('2d'), 'LDR Value', <?php echo json_encode(array_column($data, 'ldr_value')); ?>, 'green', 0, 1024); // Adjust the min and max range for LDR value
        });
    </script>
</body>
</html>
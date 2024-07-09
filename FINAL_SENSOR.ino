#include <ESP8266WiFi.h> // Include WiFi library for ESP8266
#include <ESP8266HTTPClient.h> // Include HTTP client library for ESP8266
#include <DHT.h> // Include DHT sensor library

#define DHTPIN D5 // Define DHT sensor pin
#define DHTTYPE DHT22 // Define DHT sensor type
#define LDRPIN A0 // Define LDR sensor pin

DHT dht(DHTPIN, DHTTYPE); // Initialize DHT sensor
WiFiClient client; // Initialize WiFi client

// URL to send data to
String URL = "http://192.168.35.76/dht22_project/test_data.php";

// WiFi credentials
//const char* ssid = "UUMWiFi_Guest";
//const char* password = "";
const char* ssid = "Harshini's Galaxy S20 FE 5G";
const char* password = "Harshini";

int temperature = 0; // Variable to store temperature data
int humidity = 0; // Variable to store humidity data
int ldrValue = 0; // Variable to store LDR data

void setup() {
  Serial.begin(115200); // Start serial communication at 115200 baud rate
  dht.begin(); // Initialize DHT sensor
  connectWiFi(); // Connect to WiFi
}

void loop() {
  // Check WiFi connection status
  if (WiFi.status() != WL_CONNECTED) {
    connectWiFi(); // Reconnect if not connected
  }

  Load_DHT22_Data(); // Load data from DHT22 sensor
  readLDRData(); // Load data from LDR sensor

  // Create POST data string
  String postData = "temperature=" + String(temperature) + "&humidity=" + String(humidity) + "&ldrValue=" + String(ldrValue);

  HTTPClient http; // Create HTTP client
  http.begin(client, URL); // Initialize HTTP client with URL
  http.addHeader("Content-Type", "application/x-www-form-urlencoded"); // Add content type header

  // Send POST request
  int httpCode = http.POST(postData);
  String payload = "";

  // Handle HTTP response
  if (httpCode > 0) {
    if (httpCode == HTTP_CODE_OK) {
      payload = http.getString(); // Get response payload
      Serial.println(payload); // Print payload
    } else {
      Serial.printf("[HTTP] POST... code: %d\n", httpCode); // Print HTTP response code
    }
  } else {
    Serial.printf("[HTTP] POST... failed, error: %s\n", http.errorToString(httpCode).c_str()); // Print HTTP error
  }

  http.end(); // End HTTP connection

  // Print debug information
  Serial.print("URL : ");
  Serial.println(URL);
  Serial.print("Data: ");
  Serial.println(postData);
  Serial.print("httpCode: ");
  Serial.println(httpCode);
  Serial.print("payload : ");
  Serial.println(payload);
  Serial.println("--------------------------------------------------");

  delay(5000); // Wait for 5 seconds before next loop
}

void Load_DHT22_Data() {
  temperature = dht.readTemperature(); // Read temperature from DHT sensor
  humidity = dht.readHumidity(); // Read humidity from DHT sensor

  // Check if readings are valid
  if (isnan(temperature) || isnan(humidity)) {
    Serial.println("Failed to read from DHT sensor!"); // Print error message
    temperature = 0; // Reset temperature to 0
    humidity = 0; // Reset humidity to 0
  }

  // Print sensor data
  Serial.printf("Temperature: %d °C\n", temperature);
  Serial.printf("Humidity: %d %%\n", humidity);
}

void readLDRData() {
  ldrValue = analogRead(LDRPIN); // Read LDR sensor data
  Serial.print("LDR Value: ");
  Serial.println(ldrValue); // Print LDR sensor data
}

void connectWiFi() {
  WiFi.mode(WIFI_OFF); // Turn off WiFi
  delay(1000); // Wait for 1 second
  WiFi.mode(WIFI_STA); // Set WiFi mode to station
  WiFi.begin(ssid, password); // Connect to WiFi
  Serial.println("Connecting to WiFi");

  // Wait for connection
  while (WiFi.status() != WL_CONNECTED) {
    delay(500); // Wait for 0.5 seconds
    Serial.print("."); // Print dot for each attempt
  }

  // Print connection details
  Serial.println("");
  Serial.print("Connected to ");
  Serial.println(ssid);
  Serial.print("IP address: ");
  Serial.println(WiFi.localIP());
}

/*
TRUNCATE TABLE dht22;
ALTER TABLE dht22 AUTO_INCREMENT = 1;

http://10.144.161.239/dht22_project/test_data.php
*/

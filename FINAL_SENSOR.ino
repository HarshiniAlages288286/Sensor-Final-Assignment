#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>
#include <DHT.h>

#define DHTPIN D5
#define DHTTYPE DHT22
#define LDRPIN A0

DHT dht(DHTPIN, DHTTYPE);
WiFiClient client;

String URL = "http://192.168.35.76/dht22_project/test_data.php";

//const char* ssid = "UUMWiFi_Guest";
//const char* password = "";

const char* ssid = "Harshini's Galaxy S20 FE 5G";
const char* password = "Harshini";


int temperature = 0;
int humidity = 0;
int ldrValue = 0;

void setup() {
  Serial.begin(115200);
  dht.begin();
  connectWiFi();
}

void loop() {
  if (WiFi.status() != WL_CONNECTED) {
    connectWiFi();
  }

  Load_DHT22_Data();
  readLDRData();

  String postData = "temperature=" + String(temperature) + "&humidity=" + String(humidity) + "&ldrValue=" + String(ldrValue);

  HTTPClient http;
  http.begin(client, URL);
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");

  int httpCode = http.POST(postData);
  String payload = "";

  if (httpCode > 0) {
    if (httpCode == HTTP_CODE_OK) {
      payload = http.getString();
      Serial.println(payload);
    } else {
      Serial.printf("[HTTP] POST... code: %d\n", httpCode);
    }
  } else {
    Serial.printf("[HTTP] POST... failed, error: %s\n", http.errorToString(httpCode).c_str());
  }

  http.end();

  Serial.print("URL : ");
  Serial.println(URL);
  Serial.print("Data: ");
  Serial.println(postData);
  Serial.print("httpCode: ");
  Serial.println(httpCode);
  Serial.print("payload : ");
  Serial.println(payload);
  Serial.println("--------------------------------------------------");

  delay(5000);
}

void Load_DHT22_Data() {
  temperature = dht.readTemperature();
  humidity = dht.readHumidity();

  if (isnan(temperature) || isnan(humidity)) {
    Serial.println("Failed to read from DHT sensor!");
    temperature = 0;
    humidity = 0;
  }

  Serial.printf("Temperature: %d °C\n", temperature);
  Serial.printf("Humidity: %d %%\n", humidity);
}

void readLDRData() {
  ldrValue = analogRead(LDRPIN);
  Serial.print("LDR Value: ");
  Serial.println(ldrValue);
}

void connectWiFi() {
  WiFi.mode(WIFI_OFF);
  delay(1000);
  WiFi.mode(WIFI_STA);
  WiFi.begin(ssid, password);
  Serial.println("Connecting to WiFi");

  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  

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
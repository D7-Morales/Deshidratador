/**
 * Sistema de Deshidratación Solar Inteligente
 * Trabajo de Grado - Técnico Superior en Sistemas Informáticos
 * 
 * Sketch para ESP32 + Sensor BME280 (I2C)
 * Desarrollado para compilar en Arduino IDE
 */

#include <WiFi.h>
#include <HTTPClient.h>
#include <Wire.h>
#include <Adafruit_Sensor.h>
#include <Adafruit_BME280.h>

// ==========================================
// CONFIGURACIÓN DE RED Y SERVIDOR
// ==========================================
const char* ssid = "morales";             // Cambia por el nombre de tu red WiFi
const char* password = "cambielacontra";     // Cambia por la contraseña de tu red WiFi

// URL del Endpoint de tu Servidor Laravel
// Reemplaza por la IP local de tu servidor (Ej: http://192.168.1.50/api/sensores)
const char* serverUrl = "http://192.168.1.7/api/sensores";

// ==========================================
// CONFIGURACIÓN DE SENSORES Y TIEMPOS
// ==========================================
Adafruit_BME280 bme; // Objeto del sensor BME280
unsigned long previousMillis = 0;
const long interval = 5000; // Intervalo de envío de datos (5000 ms = 5 segundos)

void setup() {
  Serial.begin(115200);
  delay(1000);
  Serial.println("\n--- Iniciando Sistema de Monitoreo ---");

  // Inicializar sensor BME280 vía I2C
  // Nota: Por lo general la dirección I2C de los módulos BME280 es 0x76 o 0x77.
  if (!bme.begin(0x76)) {
    Serial.println("¡ERROR: No se encuentra el sensor BME280! Verifique las conexiones SCL/SDA.");
    // Si falla en 0x76, intentamos en 0x77
    if (!bme.begin(0x77)) {
      Serial.println("¡ERROR: Tampoco se pudo inicializar en dirección 0x77. El sistema se detendrá!");
      while (1) { delay(10); } // Detener ejecución
    }
  }
  Serial.println("Sensor BME280 inicializado correctamente.");

  // Conectar a la red WiFi
  conectarWiFi();
}

void loop() {
  // Verificar y asegurar la conexión WiFi en cada ciclo
  if (WiFi.status() != WL_CONNECTED) {
    Serial.println("Conexión perdida. Intentando reconectar...");
    conectarWiFi();
  }

  unsigned long currentMillis = millis();
  
  // Enviar lecturas cada 5 segundos de forma no bloqueante
  if (currentMillis - previousMillis >= interval) {
    previousMillis = currentMillis;
    
    // Leer variables del sensor
    float temperatura = bme.readTemperature();
    float humedad = bme.readHumidity();
    // La lectura de presión de la librería viene en Pascales (Pa). 
    // Convertimos a Hectopascales (hPa) dividiendo por 100.
    float presion = bme.readPressure() / 100.0F;

    // Verificar si las lecturas son válidas (no son NaN)
    if (isnan(temperatura) || isnan(humedad) || isnan(presion)) {
      Serial.println("¡Error de lectura del sensor! Datos no válidos.");
      return;
    }

    // Mostrar datos por puerto serie para depuración
    Serial.println("\n--- Nueva Lectura ---");
    Serial.print("Temperatura: "); Serial.print(temperatura); Serial.println(" °C");
    Serial.print("Humedad:     "); Serial.print(humedad);     Serial.println(" %");
    Serial.print("Presión:     "); Serial.print(presion);     Serial.println(" hPa");

    // Enviar datos por HTTP POST
    enviarDatosServidor(temperatura, humedad, presion);
  }
}

// ==========================================
// FUNCIONES AUXILIARES
// ==========================================

void conectarWiFi() {
  Serial.print("Conectando a WiFi: ");
  Serial.println(ssid);
  
  WiFi.begin(ssid, password);
  
  int intentos = 0;
  // Esperar conexión (límite de 30 intentos = 15 segundos)
  while (WiFi.status() != WL_CONNECTED && intentos < 30) {
    delay(500);
    Serial.print(".");
    intentos++;
  }
  
  if (WiFi.status() == WL_CONNECTED) {
    Serial.println("\n¡Conectado exitosamente!");
    Serial.print("Dirección IP local asignada: ");
    Serial.println(WiFi.localIP());
  } else {
    Serial.println("\nNo se pudo conectar a la red WiFi. Se reintentará en el próximo ciclo.");
  }
}

void enviarDatosServidor(float temp, float hum, float pres) {
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    
    // Iniciar conexión con el endpoint del backend
    http.begin(serverUrl);
    
    // Especificar cabeceras de contenido JSON
    http.addHeader("Content-Type", "application/json");
    http.addHeader("Accept", "application/json");
    
    // Construir el string JSON de forma ligera sin librerías adicionales
    String jsonPayload = "{\"temperatura\":" + String(temp, 2) + 
                         ",\"humedad\":" + String(hum, 2) + 
                         ",\"presion\":" + String(pres, 2) + "}";
                         
    Serial.print("Enviando JSON: ");
    Serial.println(jsonPayload);
    
    // Realizar la petición POST
    int httpResponseCode = http.POST(jsonPayload);
    
    if (httpResponseCode > 0) {
      Serial.print("Código de respuesta del servidor HTTP: ");
      Serial.println(httpResponseCode);
      
      String response = http.getString();
      Serial.print("Respuesta recibida: ");
      Serial.println(response);
    } else {
      Serial.print("Error en envío POST. Código de error de conexión: ");
      Serial.println(http.errorToString(httpResponseCode).c_str());
    }
    
    // Liberar recursos
    http.end();
  } else {
    Serial.println("Imposible transmitir: Sin conexión WiFi.");
  }
}

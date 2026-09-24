/**
 * SISTEMA DE DESHIDRATACIÓN SOLAR INTELIGENTE
 * Versión 4.1 - CORREGIDO: Lógica realista para prototipo solar
 * 
 * Cambios críticos:
 * - Foco se enciende a 35°C (no 50°C) para calentar más rápido
 * - Ventilador controlado por HUMEDAD, no por temperatura
 * - Temperatura objetivo: 40-50°C (realista para 35W)
 */

#include <WiFi.h>
#include <HTTPClient.h>
#include <Wire.h>
#include <Adafruit_Sensor.h>
#include <Adafruit_BME280.h>
#include <Preferences.h>
#include <ArduinoJson.h>

// ==========================================
// CONFIGURACIÓN DE RED Y HARDWARE
// ==========================================
const char* ssid = "wifi_esp32"; 
const char* password = "contraseña_wifi";
const char* baseUrl = "http://192.168.1.7:8000";

#define PIN_RELE_VENTILADOR 26
#define PIN_RELE_FOCO 27
#define PIN_LED_WIFI 2

// ==========================================
// UMBRALES CORREGIDOS PARA PROTOTIPO REAL
// ==========================================

// FOCO HALÓGENO (Calor principal)
// Se enciende TEMPRANO para calentar la cámara
const float TEMP_FOCO_ACTIVAR = 35.0;    // °C (Enciende cuando está frío)
const float TEMP_FOCO_DESACTIVAR = 48.0; // °C (Apaga cuando ya calentó)

// VENTILADOR (Control de humedad y exceso de calor)
// SOLO se activa si hay mucha humedad O mucho calor
const float HUM_VENT_ACTIVAR = 65.0;     // % HR (Expulsa humedad de las manzanas)
const float HUM_VENT_DESACTIVAR = 50.0;  // % HR (Se apaga cuando baja la humedad)
const float TEMP_VENT_ACTIVAR = 55.0;    // °C (Solo si hay exceso de calor)
const float TEMP_VENT_DESACTIVAR = 45.0; // °C (Se apaga cuando baja la temp)

// ==========================================
// VARIABLES GLOBALES
// ==========================================
Adafruit_BME280 bme;
Preferences preferences;
unsigned long previousMillis = 0;
const long intervaloLectura = 10000;

unsigned long ultimoCheckComandos = 0;
const unsigned long intervaloCheckComandos = 5000;

bool modoManual = false;
unsigned long tiempoUltimoComandoManual = 0;
const unsigned long DURACION_MODO_MANUAL = 60000;

bool focoEncendido = false;

// ==========================================
// PROTOTIPOS
// ==========================================
void conectarWiFi();
void procesarLectura();
void controlarVentilador(float temp, float hum);
void controlarFocoAutomatico(float temp);
bool enviarLecturaAlServidor(float temp, float hum, float pres);
void guardarEnBuffer(float temp, float hum, float pres);
void sincronizarBufferPendiente();
void consultarComandosPendientes();
void procesarComandosJSON(String json);
void ejecutarAccion(String accion);
void marcarComandoEjecutado(long idComando);

// ==========================================
// SETUP
// ==========================================
void setup() {
  Serial.begin(115200);
  Serial.println("\n=== DESHIDRATADOR SOLAR v4.1 (CORREGIDO) ===");
  delay(2000);

  pinMode(PIN_LED_WIFI, OUTPUT);
  digitalWrite(PIN_LED_WIFI, LOW);

  preferences.begin("deshidratador", false);
  
  pinMode(PIN_RELE_VENTILADOR, OUTPUT);
  digitalWrite(PIN_RELE_VENTILADOR, HIGH); // APAGADO al inicio
  
  pinMode(PIN_RELE_FOCO, OUTPUT);
  digitalWrite(PIN_RELE_FOCO, HIGH);       // APAGADO al inicio

  Wire.begin(21, 22);
  if (!bme.begin(0x76) && !bme.begin(0x77)) {
    Serial.println("✗ ERROR CRÍTICO: No se encuentra BME280!");
    while (1) { digitalWrite(PIN_LED_WIFI, !digitalRead(PIN_LED_WIFI)); delay(500); }
  }

  bme.setSampling(Adafruit_BME280::MODE_NORMAL, Adafruit_BME280::SAMPLING_X2,
    Adafruit_BME280::SAMPLING_X16, Adafruit_BME280::SAMPLING_X2,
    Adafruit_BME280::FILTER_X16, Adafruit_BME280::STANDBY_MS_500);
  
  conectarWiFi();
  Serial.println("\n=== SISTEMA LISTO - INICIANDO SECADO CORREGIDO ===\n");
}

// ==========================================
// LOOP PRINCIPAL
// ==========================================
void loop() {
  if (WiFi.status() != WL_CONNECTED) {
    digitalWrite(PIN_LED_WIFI, HIGH);
    conectarWiFi();
  } else {
    digitalWrite(PIN_LED_WIFI, LOW);
  }

  unsigned long currentMillis = millis();
  
  if (currentMillis - previousMillis >= intervaloLectura) {
    previousMillis = currentMillis;
    procesarLectura();
  }

  if (currentMillis - ultimoCheckComandos >= intervaloCheckComandos) {
    ultimoCheckComandos = currentMillis;
    consultarComandosPendientes();
  }

  delay(100);
}

// ==========================================
// PROCESAR LECTURA
// ==========================================
void procesarLectura() {
  float temperatura = bme.readTemperature();
  float humedad = bme.readHumidity();
  float presion = bme.readPressure() / 100.0F;

  bool datosValidos = true;
  
  if (isnan(temperatura) || isnan(humedad) || isnan(presion)) {
    datosValidos = false;
    Serial.println("✗ Sensor no responde (NaN)");
  }
  else if (temperatura < 0 || temperatura > 85) {
    datosValidos = false;
    Serial.print("✗ Temp fuera de rango: "); Serial.println(temperatura);
  }
  else if (humedad < 0 || humedad > 100) {
    datosValidos = false;
    Serial.print("✗ Humedad fuera de rango: "); Serial.println(humedad);
  }
  else if (presion < 650 || presion > 1100) {
    datosValidos = false;
    Serial.print(" Presión fuera de rango: "); Serial.println(presion);
  }

  if (datosValidos) {
    Serial.println("\n========== MONITOREO CÁMARA ==========");
    Serial.print("🌡️ Temperatura: "); Serial.print(temperatura, 2); Serial.println(" °C");
    Serial.print("💧 Humedad Rel.: "); Serial.print(humedad, 2); Serial.println(" %");
    Serial.print("🔽 Presión: "); Serial.print(presion, 2); Serial.println(" hPa");
    Serial.print("💡 Foco: "); Serial.println(focoEncendido ? "ENCENDIDO" : "APAGADO");

    // Modo Manual vs Automático
    if (modoManual) {
      unsigned long tiempoTranscurrido = millis() - tiempoUltimoComandoManual;
      if (tiempoTranscurrido > DURACION_MODO_MANUAL) {
        modoManual = false;
        Serial.println("[SISTEMA] ⏱️ Modo manual expirado. Retomando control AUTOMÁTICO.");
      } else {
        unsigned long segundosRestantes = (DURACION_MODO_MANUAL - tiempoTranscurrido) / 1000;
        Serial.print("[SISTEMA] Modo MANUAL activo. Auto en: ");
        Serial.print(segundosRestantes);
        Serial.println("s.");
      }
    }

    // CONTROLES AUTOMÁTICOS
    if (!modoManual) {
      controlarFocoAutomatico(temperatura);      // PRIMERO: Calentar
      controlarVentilador(temperatura, humedad); // SEGUNDO: Regular humedad
    }

    // Envío a Laravel
    if (WiFi.status() == WL_CONNECTED) {
      if (enviarLecturaAlServidor(temperatura, humedad, presion)) {
        sincronizarBufferPendiente();
      } else {
        guardarEnBuffer(temperatura, humedad, presion);
      }
    } else {
      guardarEnBuffer(temperatura, humedad, presion);
    }
    Serial.println("========================================\n");
  } else {
    Serial.println("✗ Datos INVÁLIDOS - Omitiendo lectura");
  }
}

// ==========================================
// CONTROL DEL FOCO (CALENTAMIENTO AGRESIVO)
// ==========================================
void controlarFocoAutomatico(float temp) {
  bool estaEncendido = (digitalRead(PIN_RELE_FOCO) == LOW);
  
  if (!estaEncendido) {
    // Si está APAGADO y la temp es menor a 35°C, ENCENDER
    if (temp < TEMP_FOCO_ACTIVAR) {
      digitalWrite(PIN_RELE_FOCO, LOW);
      focoEncendido = true;
      Serial.println("💡 FOCO: ENCENDIDO (Calentando cámara desde " + String(temp, 1) + "°C)");
    }
  } else {
    // Si está ENCENDIDO y la temp superó 48°C, APAGAR
    if (temp > TEMP_FOCO_DESACTIVAR) {
      digitalWrite(PIN_RELE_FOCO, HIGH);
      focoEncendido = false;
      Serial.println("⏹️ FOCO: APAGADO (Temp óptima alcanzada: " + String(temp, 1) + "°C)");
    }
  }
}

// ==========================================
// CONTROL DEL VENTILADOR (SOLO HUMEDAD/EXCESO CALOR)
// ==========================================
void controlarVentilador(float temp, float hum) {
  bool estaEncendido = (digitalRead(PIN_RELE_VENTILADOR) == LOW);
  bool debeActivarse = false;
  bool debeDesactivarse = false;

  if (!estaEncendido) {
    // Encender SOLO si hay mucha humedad O exceso de calor
    if (hum > HUM_VENT_ACTIVAR || temp > TEMP_VENT_ACTIVAR) {
      debeActivarse = true;
    }
  } else {
    // Apagar cuando la humedad Y temperatura bajen
    if (hum < HUM_VENT_DESACTIVAR && temp < TEMP_VENT_DESACTIVAR) {
      debeDesactivarse = true;
    }
  }

  if (debeActivarse) {
    digitalWrite(PIN_RELE_VENTILADOR, LOW);
    Serial.println("✅ VENTILADOR: ACTIVADO (HR: " + String(hum, 0) + "% o Temp: " + String(temp, 1) + "°C)");
  } 
  else if (debeDesactivarse) {
    digitalWrite(PIN_RELE_VENTILADOR, HIGH);
    Serial.println("⏹️ VENTILADOR: DESACTIVADO (Condiciones óptimas)");
  }
  else {
    Serial.print("🌀 VENTILADOR: ");
    Serial.println(estaEncendido ? "MANTENIENDO" : "EN ESPERA");
  }
}

// ==========================================
// COMUNICACIÓN CON LARAVEL
// ==========================================
bool enviarLecturaAlServidor(float temp, float hum, float pres) {
  HTTPClient http;
  http.begin(String(baseUrl) + "/api/readings");
  http.addHeader("Content-Type", "application/json");

  bool ventiladorActivado = (digitalRead(PIN_RELE_VENTILADOR) == LOW);
  
  String json = "{\"temperatura\":" + String(temp, 2) + 
                ",\"humedad\":" + String(hum, 2) + 
                ",\"presion\":" + String(pres, 2) + 
                ",\"id_sensor\":1," +
                "\"ventilador_activado\":" + String(ventiladorActivado ? "true" : "false") + "," +
                "\"foco_activado\":" + String(focoEncendido ? "true" : "false") + "}";

  int code = http.POST(json);
  http.end();
  return (code == 200 || code == 201);
}

void guardarEnBuffer(float temp, float hum, float pres) {
  int idx = preferences.getInt("buffer_count", 0);
  if (idx < 50) {
    preferences.putFloat(("t" + String(idx)).c_str(), temp);
    preferences.putFloat(("h" + String(idx)).c_str(), hum);
    preferences.putFloat(("p" + String(idx)).c_str(), pres);
    preferences.putInt("buffer_count", idx + 1);
  }
}

void sincronizarBufferPendiente() {
  int count = preferences.getInt("buffer_count", 0);
  if (count == 0) return;
  for (int i = 0; i < count; i++) {
    float t = preferences.getFloat(("t" + String(i)).c_str(), 0);
    if (t != 0) enviarLecturaAlServidor(t, preferences.getFloat(("h" + String(i)).c_str(), 0), preferences.getFloat(("p" + String(i)).c_str(), 0));
    delay(200);
  }
  preferences.putInt("buffer_count", 0);
}

void conectarWiFi() {
  Serial.print("Conectando a: "); Serial.println(ssid);
  WiFi.begin(ssid, password);
  int intentos = 0;
  while (WiFi.status() != WL_CONNECTED && intentos < 30) {
    delay(500);
    Serial.print(".");
    digitalWrite(PIN_LED_WIFI, !digitalRead(PIN_LED_WIFI));
    intentos++;
  }
  if (WiFi.status() == WL_CONNECTED) {
    Serial.println("\n✓ WiFi conectado!");
    Serial.print("IP: "); Serial.println(WiFi.localIP());
  } else {
    Serial.println("\n✗ No se pudo conectar al WiFi");
  }
}

// ==========================================
// COMANDOS REMOTOS (MANUAL)
// ==========================================
void consultarComandosPendientes() {
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    http.begin(String(baseUrl) + "/api/comandos/pendientes");
    if (http.GET() == HTTP_CODE_OK) {
      procesarComandosJSON(http.getString());
    }
    http.end();
  }
}

void procesarComandosJSON(String json) {
  StaticJsonDocument<1024> doc;
  if (deserializeJson(doc, json)) return;
  
  if (doc.containsKey("comandos")) {
    for (JsonObject comando : doc["comandos"].as<JsonArray>()) {
      long id = comando["id_comando"];
      String accion = comando["accion"].as<String>();
      
      Serial.print("\n[COMANDO] ID: ");
      Serial.print(id);
      Serial.print(" | Acción: ");
      Serial.println(accion);
      
      ejecutarAccion(accion);
      marcarComandoEjecutado(id);
      delay(500);
    }
  }
}

void ejecutarAccion(String accion) {
  modoManual = true;
  tiempoUltimoComandoManual = millis();
  
  if (accion == "activar" || accion == "activar_ventilador") {
    Serial.println("▶️ MANUAL: Activando ventilador");
    digitalWrite(PIN_RELE_VENTILADOR, LOW); 
  } 
  else if (accion == "desactivar" || accion == "desactivar_ventilador") {
    Serial.println("⏹️ MANUAL: Desactivando ventilador");
    digitalWrite(PIN_RELE_VENTILADOR, HIGH); 
  }
  else if (accion == "encender_foco" || accion == "activar_foco") {
    Serial.println("💡 MANUAL: Encendiendo foco");
    digitalWrite(PIN_RELE_FOCO, LOW);
    focoEncendido = true;
  } 
  else if (accion == "apagar_foco" || accion == "desactivar_foco") {
    Serial.println("⏹️ MANUAL: Apagando foco");
    digitalWrite(PIN_RELE_FOCO, HIGH);
    focoEncendido = false;
  }
}

void marcarComandoEjecutado(long idComando) {
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    String url = String(baseUrl) + "/api/comandos/" + String(idComando) + "/ejecutado";
    http.begin(url);
    http.addHeader("Content-Type", "application/json");
    http.setTimeout(5000);
    
    for (int intento = 1; intento <= 3; intento++) {
      int httpCode = http.POST("{}");
      Serial.print("[MARCAR] ID: "); Serial.print(idComando);
      Serial.print(" - Intento "); Serial.print(intento);
      Serial.print(" -> HTTP: "); Serial.println(httpCode);
      
      if (httpCode == 200) {
        Serial.println("✅ ÉXITO: Comando ejecutado.");
        http.end();
        return;
      }
      if (intento < 3) delay(1000);
    }
    Serial.println("❌ ERROR: No se pudo marcar el comando.");
    http.end();
  }
}
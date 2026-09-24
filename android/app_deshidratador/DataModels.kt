package com.example.deshidratadorsolar

import com.google.gson.annotations.SerializedName

// Response model for /api/metrics
data class MetricsResponse(
    @SerializedName("success") val success: Boolean,
    @SerializedName("metrics") val metrics: MetricsData?,
    @SerializedName("proceso_activo") val procesoActivo: ProcesoActivoData?,
    @SerializedName("ultima_lectura") val ultimaLectura: LecturaData?
)

data class MetricsData(
    @SerializedName("total_procesos") val totalProcesos: Int,
    @SerializedName("peso_inicial_total_kg") val pesoInicialTotalKg: Double,
    @SerializedName("peso_final_total_kg") val pesoFinalTotalKg: Double,
    @SerializedName("porcentaje_secado_promedio") val porcentajeSecadoPromedio: Double,
    @SerializedName("tiempo_total_horas") val tiempoTotalHoras: Double,
    @SerializedName("temperatura_promedio") val temperaturaPromedio: Double,
    @SerializedName("humedad_promedio") val humedadPromedio: Double,
    @SerializedName("total_alertas") val totalAlertas: Int,
    @SerializedName("fruta_recuperada_kg") val frutaRecuperadaKg: Double,
    @SerializedName("is_online") val isOnline: Boolean,
    @SerializedName("ultima_conexion") val ultimaConexion: String
)

data class ProcesoActivoData(
    @SerializedName("id_proceso") val idProceso: Long,
    @SerializedName("nombre_fruta") val nombreFruta: String,
    @SerializedName("lote") val lote: String,
    @SerializedName("bandeja") val bandeja: Int,
    @SerializedName("fecha_inicio") val fechaInicio: String,
    @SerializedName("peso_inicial") val pesoInicial: Double
)

data class LecturaData(
    @SerializedName("temperatura") val temperatura: Double,
    @SerializedName("humedad") val humedad: Double,
    @SerializedName("presion") val presion: Double,
    @SerializedName("fecha_hora") val fechaHora: String
)

// Response model for /api/alerts
data class AlertsResponse(
    @SerializedName("success") val success: Boolean,
    @SerializedName("alerts") val alerts: List<AlertItem>
)

data class AlertItem(
    @SerializedName("id_alerta") val idAlerta: Long,
    @SerializedName("tipo_alerta") val tipoAlerta: String,
    @SerializedName("mensaje") val mensaje: String,
    @SerializedName("fecha_alerta") val fechaAlerta: String
)

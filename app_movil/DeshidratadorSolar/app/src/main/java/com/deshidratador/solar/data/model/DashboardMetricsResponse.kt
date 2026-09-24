package com.deshidratador.solar.data.model

import com.google.gson.annotations.SerializedName

data class DashboardMetricsResponse(
    @SerializedName("total_procesos") val totalProcesos: Int,
    @SerializedName("peso_inicial_promedio") val pesoInicialPromedio: Double,
    @SerializedName("peso_final_promedio") val pesoFinalPromedio: Double,
    @SerializedName("porcentaje_secado_promedio") val porcentajeSecadoPromedio: Double,
    @SerializedName("tiempo_total_deshidratacion") val tiempoTotalDeshidratacion: Double,
    @SerializedName("temperatura_promedio") val temperaturaPromedio: Double,
    @SerializedName("humedad_promedio") val humedadPromedio: Double,
    @SerializedName("total_alertas") val totalAlertas: Int,
    @SerializedName("fruta_recuperada_kg") val frutaRecuperadaKg: Double,
    @SerializedName("is_online") val isOnline: Boolean,
    @SerializedName("estado_ventilador") val estadoVentilador: String,
    @SerializedName("ultima_conexion") val ultimaConexion: String
)
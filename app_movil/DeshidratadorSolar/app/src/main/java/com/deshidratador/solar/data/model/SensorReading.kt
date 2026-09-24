package com.deshidratador.solar.data.model

import com.google.gson.annotations.SerializedName
import java.text.SimpleDateFormat
import java.util.Date
import java.util.Locale

data class SensorReading(
    @SerializedName("id_lectura") val id: Long,
    @SerializedName("id_sensor") val sensorId: Long,
    @SerializedName("id_proceso") val procesoId: Long?, // ← CAMBIADO de id_carga
    @SerializedName("temperatura") val temperatura: Double,
    @SerializedName("humedad") val humedad: Double,
    @SerializedName("presion") val presion: Double,
    // ← ELIMINADO: radiacion_solar ya no existe en la BD
    @SerializedName("fecha_hora") val fechaHora: String,
    @SerializedName("created_at") val createdAt: String? = null
) {
    fun getFechaFormateada(): String {
        return try {
            val inputFormat = SimpleDateFormat("yyyy-MM-dd HH:mm:ss", Locale.getDefault())
            val outputFormat = SimpleDateFormat("dd/MM/yyyy HH:mm", Locale.getDefault())
            val date = inputFormat.parse(fechaHora)
            outputFormat.format(date ?: Date())
        } catch (e: Exception) {
            fechaHora
        }
    }
}
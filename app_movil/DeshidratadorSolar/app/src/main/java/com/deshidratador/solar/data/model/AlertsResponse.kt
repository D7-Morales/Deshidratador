package com.deshidratador.solar.data.model

import com.google.gson.annotations.SerializedName
import java.text.SimpleDateFormat
import java.util.Date
import java.util.Locale

data class AlertsResponse(
    @SerializedName("id_alerta") val id: Long,
    @SerializedName("id_proceso") val procesoId: Long,
    @SerializedName("id_lectura") val lecturaId: Long?,
    @SerializedName("tipo_alerta") val tipo: String,
    @SerializedName("mensaje") val mensaje: String,
    @SerializedName("atendida") val atendida: Boolean,
    @SerializedName("fecha_alerta") val fechaAlerta: String
) {
    fun getFechaFormateada(): String {
        return try {
            val inputFormat = SimpleDateFormat("yyyy-MM-dd HH:mm:ss", Locale.getDefault())
            val outputFormat = SimpleDateFormat("dd/MM/yyyy HH:mm", Locale.getDefault())
            val date = inputFormat.parse(fechaAlerta)
            outputFormat.format(date ?: Date())
        } catch (e: Exception) {
            fechaAlerta
        }
    }

    fun esCritica(): Boolean {
        return tipo.contains("temperatura") || tipo.contains("error")
    }
}
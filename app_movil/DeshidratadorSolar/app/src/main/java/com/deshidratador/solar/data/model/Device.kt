package com.deshidratador.solar.data.model

import com.google.gson.annotations.SerializedName

data class Device(
    @SerializedName("id_dispositivo") val id: Long,
    @SerializedName("id_deshidratador") val deshidratadorId: Long,
    @SerializedName("nombre_dispositivo") val nombre: String,
    @SerializedName("tipo_dispositivo") val tipo: String,
    @SerializedName("estado_actual") val estado: String,
    @SerializedName("pin_control") val pinControl: Int? = null
) {
    fun estaActivo(): Boolean = estado == "activo" || estado == "encendido"
}
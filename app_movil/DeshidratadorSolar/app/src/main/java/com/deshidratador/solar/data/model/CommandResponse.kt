package com.deshidratador.solar.data.model


import com.google.gson.annotations.SerializedName

// Esta clase representa la respuesta completa del servidor
data class CommandResponse(
    @SerializedName("success") val success: Boolean,
    @SerializedName("message") val message: String,
    @SerializedName("data") val data: CommandData?
)

// Esta clase representa solo la parte "data" de la respuesta
data class CommandData(
    @SerializedName("id_comando") val idComando: Long,
    @SerializedName("estado") val estado: String
)
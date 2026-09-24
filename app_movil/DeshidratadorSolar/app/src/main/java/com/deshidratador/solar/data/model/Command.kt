package com.deshidratador.solar.data.model

import com.google.gson.annotations.SerializedName

data class Command(
    @SerializedName("id_comando") val id: Long? = null,
    @SerializedName("id_dispositivo") val dispositivoId: Long,
    @SerializedName("id_usuario") val usuarioId: Long,
    @SerializedName("id_proceso") val procesoId: Long? = null,
    @SerializedName("accion") val accion: String,
    @SerializedName("origen") val origen: String = "manual",
    @SerializedName("estado_ejecucion") val estadoEjecucion: String? = "pendiente",
    @SerializedName("fecha_envio") val fechaEnvio: String? = null,
    @SerializedName("fecha_respuesta") val fechaRespuesta: String? = null
)
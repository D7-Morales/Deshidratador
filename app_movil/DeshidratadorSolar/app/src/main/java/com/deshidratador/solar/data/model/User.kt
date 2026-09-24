package com.deshidratador.solar.data.model

import com.google.gson.annotations.SerializedName

data class User(
    @SerializedName("id_usuario") val id_usuario: Long,
    @SerializedName("ci_usuario") val ci_usuario: String,
    
    @SerializedName("nombres_usuario") val nombres_usuario: String,
    @SerializedName("apellidos_usuario") val apellidos_usuario: String,
    @SerializedName("email_usuario") val email_usuario: String,
    @SerializedName("id_rol") val id_rol: Long,
    @SerializedName("rol") val rol: Role? = null,
    @SerializedName("estado_usuario") val estado: String = "activo",
    @SerializedName("token") val token: String
) {
    val nombreCompleto: String
        get() = "$nombres_usuario $apellidos_usuario"

    fun esAdmin(): Boolean = rol    ?.nombre_rol == "admin"
}

data class Role(
    @SerializedName("id_rol") val id_rol: Long,
    @SerializedName("nombre_rol") val nombre_rol: String,
    @SerializedName("descripcion") val descripcion: String
)
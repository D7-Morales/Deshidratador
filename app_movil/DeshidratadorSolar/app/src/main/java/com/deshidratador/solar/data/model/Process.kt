package com.deshidratador.solar.data.model

import com.google.gson.annotations.SerializedName

data class Process(
    @SerializedName("id_proceso") val idProceso: Long, // ← CAMBIADO de id_carga
    @SerializedName("numero_lote") val numeroLote: String?,
    @SerializedName("id_fruta") val frutaId: Long,
    @SerializedName("id_usuario") val usuarioId: Long,
    @SerializedName("id_deshidratador") val deshidratadorId: Long,
    @SerializedName("fecha_inicio") val fechaInicio: String,
    @SerializedName("fecha_fin") val fechaFin: String?,
    @SerializedName("peso_inicial_gramos") val pesoInicial: Double,
    @SerializedName("peso_final_gramos") val pesoFinal: Double?,
    @SerializedName("estado_proceso") val estado: String,
    @SerializedName("fruta") val fruta: Fruit? = null,
    @SerializedName("duracion_horas") val duracionHoras: Double? = null
) {
    // Esta función es EXCELENTE para la métrica #3 que pidió el tutor (% de secado)
    fun getProgreso(): Int {
        return if (pesoFinal != null && pesoInicial > 0) {
            ((pesoInicial - pesoFinal) / pesoInicial * 100).toInt().coerceIn(0, 100)
        } else 0
    }
}

data class Fruit(
    @SerializedName("id_fruta") val id: Long,
    @SerializedName("nombre_fruta") val nombre: String,
    @SerializedName("temperatura_recomendada") val temperaturaRecomendada: Double,
    @SerializedName("humedad_recomendada") val humedadRecomendada: Double,
    @SerializedName("tiempo_estimado_horas") val tiempoEstimado: Int
)
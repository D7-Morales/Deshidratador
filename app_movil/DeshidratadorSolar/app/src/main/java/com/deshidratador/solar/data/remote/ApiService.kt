package com.deshidratador.solar.data.remote

import com.deshidratador.solar.data.model.*
import retrofit2.Response
import retrofit2.http.*

interface ApiService {

    // ===== AUTENTICACIÓN =====
    @POST("api/login")
    suspend fun login(@Body credentials: Map<String, String>): Response<LoginResponse>

    @GET("api/user")
    suspend fun getUser(@Header("Authorization") token: String): Response<User>

    // ===== MÉTRICAS DEL DASHBOARD =====
    @GET("api/metrics")
    suspend fun getDashboardMetrics(
        @Query("proceso_id") procesoId: Long? = null
    ): Response<DashboardMetricsResponse>

    // ===== ALERTAS =====
    @GET("api/alerts")
    suspend fun getAlerts(
        @Query("proceso_id") procesoId: Long? = null,
        @Query("limit") limit: Int = 50
    ): Response<List<AlertsResponse>>

    // ===== LECTURAS DE SENSORES =====
    @GET("api/readings/latest")
    suspend fun getLatestReading(): Response<SensorReading>

    @GET("api/readings")
    suspend fun getReadings(
        @Query("proceso_id") procesoId: Long?,
        @Query("limit") limit: Int = 50
    ): Response<List<SensorReading>>

    // ===== PROCESOS DE DESHIDRATACIÓN =====
    @GET("api/procesos")
    suspend fun getProcesses(
        @Query("estado") estado: String? = null
    ): Response<List<Process>>

    @GET("api/procesos/{id}")
    suspend fun getProcessById(@Path("id") id: Long): Response<Process>

    @POST("api/procesos")
    suspend fun createProcess(@Body process: Process): Response<Process>

    // ===== FRUTAS =====
    @GET("api/frutas")
    suspend fun getFruits(): Response<List<Fruit>>

    // ===== DISPOSITIVOS Y COMANDOS =====
    @GET("api/dispositivos")
    suspend fun getDevices(): Response<List<Device>>

    // ← AQUÍ ESTÁ EL CAMBIO: Ahora devuelve Response<CommandResponse>
    @POST("api/comandos")
    suspend fun sendCommand(@Body command: Command): Response<CommandResponse>

    // Marcar alerta como atendida
    @POST("api/alerts/{id}/atender")
    suspend fun marcarAlertaAtendida(@Path("id") id: Long): Response<Map<String, Any>>

}
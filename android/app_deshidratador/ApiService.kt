package com.example.deshidratadorsolar

import retrofit2.Retrofit
import retrofit2.converter.gson.GsonConverterFactory
import retrofit2.http.GET

interface ApiService {
    @GET("api/metrics")
    suspend fun getMetrics(): MetricsResponse

    @GET("api/alerts")
    suspend fun getAlerts(): AlertsResponse

    companion object {
        // IP de tu servidor Laravel configurada según tus requerimientos
        private const val BASE_URL = "http://192.168.1.6:8000/"

        fun create(): ApiService {
            return Retrofit.Builder()
                .baseUrl(BASE_URL)
                .addConverterFactory(GsonConverterFactory.create())
                .build()
                .create(ApiService::class.java)
        }
    }
}

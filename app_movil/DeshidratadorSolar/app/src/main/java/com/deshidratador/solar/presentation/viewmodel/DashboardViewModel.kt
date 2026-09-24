package com.deshidratador.solar.presentation.viewmodel

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.deshidratador.solar.data.model.AlertsResponse
import com.deshidratador.solar.data.model.DashboardMetricsResponse
import com.deshidratador.solar.data.model.SensorReading
import com.deshidratador.solar.data.remote.ApiClient
import kotlinx.coroutines.launch

class DashboardViewModel : ViewModel() {

    // Métricas del dashboard (las 7 que pidió el tutor)
    private val _metrics = MutableLiveData<DashboardMetricsResponse>()
    val metrics: LiveData<DashboardMetricsResponse> = _metrics

    // Última lectura del sensor
    private val _latestReading = MutableLiveData<SensorReading>()
    val latestReading: LiveData<SensorReading> = _latestReading

    // Alertas recientes
    private val _alerts = MutableLiveData<List<AlertsResponse>>()
    val alerts: LiveData<List<AlertsResponse>> = _alerts

    // Estado de carga
    private val _isLoading = MutableLiveData<Boolean>()
    val isLoading: LiveData<Boolean> = _isLoading

    // Mensaje de error
    private val _errorMessage = MutableLiveData<String>()
    val errorMessage: LiveData<String> = _errorMessage

    // Estado de conexión (Online/Offline)
    private val _isOnline = MutableLiveData<Boolean>(true)
    val isOnline: LiveData<Boolean> = _isOnline

    /**
     * Cargar las 7 métricas del dashboard
     */
    fun loadMetrics(procesoId: Long? = null) {
        viewModelScope.launch {
            _isLoading.value = true
            try {
                val response = ApiClient.apiService.getDashboardMetrics(procesoId)
                if (response.isSuccessful && response.body() != null) {
                    _metrics.value = response.body()
                    _isOnline.value = true
                } else {
                    _errorMessage.value = "Error al cargar métricas"
                }
            } catch (e: Exception) {
                _errorMessage.value = "Error de conexión: ${e.message}"
                _isOnline.value = false
            } finally {
                _isLoading.value = false
            }
        }
    }

    /**
     * Cargar última lectura del sensor
     */
    fun loadLatestReading() {
        viewModelScope.launch {
            try {
                val response = ApiClient.apiService.getLatestReading()
                if (response.isSuccessful && response.body() != null) {
                    _latestReading.value = response.body()
                    _isOnline.value = true
                }
            } catch (e: Exception) {
                _isOnline.value = false
            }
        }
    }

    /**
     * Cargar alertas recientes
     */
    fun loadAlerts(procesoId: Long? = null, limit: Int = 50) {
        viewModelScope.launch {
            try {
                val response = ApiClient.apiService.getAlerts(procesoId, limit)
                if (response.isSuccessful && response.body() != null) {
                    _alerts.value = response.body()
                }
            } catch (e: Exception) {
                // Error silencioso para no molestar al usuario
            }
        }
    }
}
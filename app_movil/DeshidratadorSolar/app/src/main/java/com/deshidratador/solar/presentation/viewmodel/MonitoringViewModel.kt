package com.deshidratador.solar.presentation.viewmodel

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.deshidratador.solar.data.model.SensorReading
import com.deshidratador.solar.data.remote.ApiClient
import kotlinx.coroutines.launch

class MonitoringViewModel : ViewModel() {

    // Lista de lecturas para mostrar en gráfico/historial
    private val _readings = MutableLiveData<List<SensorReading>>()
    val readings: LiveData<List<SensorReading>> = _readings

    // Lectura actual en tiempo real
    private val _currentReading = MutableLiveData<SensorReading>()
    val currentReading: LiveData<SensorReading> = _currentReading

    // Estado de carga
    private val _isLoading = MutableLiveData<Boolean>()
    val isLoading: LiveData<Boolean> = _isLoading

    // Mensaje de error
    private val _errorMessage = MutableLiveData<String>()
    val errorMessage: LiveData<String> = _errorMessage

    // Estado de conexión
    private val _isConnected = MutableLiveData<Boolean>(true)
    val isConnected: LiveData<Boolean> = _isConnected

    /**
     * Cargar lecturas históricas de un proceso
     */
    fun loadReadings(procesoId: Long? = null, limit: Int = 100) {
        viewModelScope.launch {
            _isLoading.value = true
            try {
                val response = ApiClient.apiService.getReadings(procesoId, limit)
                if (response.isSuccessful && response.body() != null) {
                    _readings.value = response.body()
                    _isConnected.value = true

                    // Si hay lecturas, actualizar la más reciente
                    response.body()?.lastOrNull()?.let {
                        _currentReading.value = it
                    }
                } else {
                    _errorMessage.value = "Error al cargar lecturas"
                }
            } catch (e: Exception) {
                _errorMessage.value = "Error de conexión: ${e.message}"
                _isConnected.value = false
            } finally {
                _isLoading.value = false
            }
        }
    }

    /**
     * Actualizar lectura actual (se llama periódicamente)
     */
    fun refreshCurrentReading() {
        viewModelScope.launch {
            try {
                val response = ApiClient.apiService.getLatestReading()
                if (response.isSuccessful && response.body() != null) {
                    _currentReading.value = response.body()
                    _isConnected.value = true
                }
            } catch (e: Exception) {
                _isConnected.value = false
            }
        }
    }
}
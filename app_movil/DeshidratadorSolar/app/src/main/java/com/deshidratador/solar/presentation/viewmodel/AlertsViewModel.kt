package com.deshidratador.solar.presentation.viewmodel

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.deshidratador.solar.data.model.AlertsResponse
import com.deshidratador.solar.data.remote.ApiClient
import kotlinx.coroutines.launch

class AlertsViewModel : ViewModel() {

    // Lista de todas las alertas
    private val _alerts = MutableLiveData<List<AlertsResponse>>()
    val alerts: LiveData<List<AlertsResponse>> = _alerts

    // Alertas filtradas (solo críticas o solo atendidas)
    private val _filteredAlerts = MutableLiveData<List<AlertsResponse>>()
    val filteredAlerts: LiveData<List<AlertsResponse>> = _filteredAlerts

    // Estado de carga
    private val _isLoading = MutableLiveData<Boolean>()
    val isLoading: LiveData<Boolean> = _isLoading

    // Mensaje de error
    private val _errorMessage = MutableLiveData<String>()
    val errorMessage: LiveData<String> = _errorMessage

    /**
     * Cargar todas las alertas
     */
    fun loadAlerts(procesoId: Long? = null, limit: Int = 100) {
        viewModelScope.launch {
            _isLoading.value = true
            try {
                val response = ApiClient.apiService.getAlerts(procesoId, limit)
                if (response.isSuccessful && response.body() != null) {
                    _alerts.value = response.body()
                    _filteredAlerts.value = response.body()
                } else {
                    _errorMessage.value = "Error al cargar alertas"
                }
            } catch (e: Exception) {
                _errorMessage.value = "Error de conexión: ${e.message}"
            } finally {
                _isLoading.value = false
            }
        }
    }

    /**
     * Filtrar alertas por tipo (críticas vs normales)
     */
    fun filterAlerts(showOnlyCritical: Boolean) {
        val allAlerts = _alerts.value ?: return

        val filtered = if (showOnlyCritical) {
            allAlerts.filter { it.esCritica() }
        } else {
            allAlerts
        }

        _filteredAlerts.value = filtered
    }

    /**
     * Obtener conteo de alertas por tipo
     */
    fun getAlertsCount(): Map<String, Int> {
        val allAlerts = _alerts.value ?: return emptyMap()

        return mapOf(
            "total" to allAlerts.size,
            "criticas" to allAlerts.count { it.esCritica() },
            "atendidas" to allAlerts.count { it.atendida },
            "pendientes" to allAlerts.count { !it.atendida }
        )
    }
}
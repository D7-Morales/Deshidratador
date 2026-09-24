package com.deshidratador.solar.presentation.viewmodel

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.deshidratador.solar.data.model.Process
import com.deshidratador.solar.data.remote.ApiClient
import kotlinx.coroutines.launch

class ProcessViewModel : ViewModel() {

    // Lista de procesos
    private val _processes = MutableLiveData<List<Process>>()
    val processes: LiveData<List<Process>> = _processes

    // Proceso seleccionado
    private val _selectedProcess = MutableLiveData<Process>()
    val selectedProcess: LiveData<Process> = _selectedProcess

    // Estado de carga
    private val _isLoading = MutableLiveData<Boolean>()
    val isLoading: LiveData<Boolean> = _isLoading

    // Mensaje de error
    private val _errorMessage = MutableLiveData<String>()
    val errorMessage: LiveData<String> = _errorMessage

    /**
     * Cargar todos los procesos
     */
    fun loadProcesses(estado: String? = null) {
        viewModelScope.launch {
            _isLoading.value = true
            try {
                val response = ApiClient.apiService.getProcesses(estado)
                if (response.isSuccessful && response.body() != null) {
                    _processes.value = response.body()
                } else {
                    _errorMessage.value = "Error al cargar procesos"
                }
            } catch (e: Exception) {
                _errorMessage.value = "Error de conexión: ${e.message}"
            } finally {
                _isLoading.value = false
            }
        }
    }

    /**
     * Cargar un proceso específico por ID
     */
    fun loadProcessById(id: Long) {
        viewModelScope.launch {
            _isLoading.value = true
            try {
                val response = ApiClient.apiService.getProcessById(id)
                if (response.isSuccessful && response.body() != null) {
                    _selectedProcess.value = response.body()
                } else {
                    _errorMessage.value = "Proceso no encontrado"
                }
            } catch (e: Exception) {
                _errorMessage.value = "Error de conexión: ${e.message}"
            } finally {
                _isLoading.value = false
            }
        }
    }

    /**
     * Seleccionar un proceso de la lista
     */
    fun selectProcess(process: Process) {
        _selectedProcess.value = process
    }

    /**
     * Obtener procesos activos
     */
    fun getActiveProcesses(): List<Process> {
        return _processes.value?.filter { it.estado == "activo" } ?: emptyList()
    }
}
package com.deshidratador.solar.presentation.control

import android.app.AlertDialog
import android.os.Bundle
import android.os.Handler
import android.os.Looper
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Toast
import androidx.fragment.app.Fragment
import androidx.lifecycle.lifecycleScope
import com.deshidratador.solar.R
import com.deshidratador.solar.data.model.Command
import com.deshidratador.solar.data.prefs.SessionManager
import com.deshidratador.solar.data.remote.ApiClient
import com.google.android.material.button.MaterialButton
import kotlinx.coroutines.launch

class ControlFragment : Fragment() {

    private lateinit var sessionManager: SessionManager
    private lateinit var btnActivarVent: MaterialButton
    private lateinit var btnDesactivarVent: MaterialButton
    private lateinit var btnEncenderFoco: MaterialButton
    private lateinit var btnApagarFoco: MaterialButton

    private var isProcessing = false

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        return inflater.inflate(R.layout.fragment_control, container, false)
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)

        sessionManager = SessionManager(requireContext())

        btnActivarVent = view.findViewById(R.id.btnActivarVentilador)
        btnDesactivarVent = view.findViewById(R.id.btnDesactivarVentilador)
        btnEncenderFoco = view.findViewById(R.id.btnEncenderFoco)
        btnApagarFoco = view.findViewById(R.id.btnApagarFoco)

        // ✅ VENTILADOR
        btnActivarVent.setOnClickListener {
            pedirConfirmacion("Activar Ventilador", "¿Encender ventilador?", "activar_ventilador")
        }
        btnDesactivarVent.setOnClickListener {
            pedirConfirmacion("Desactivar Ventilador", "¿Apagar ventilador?", "desactivar_ventilador")
        }

        // ✅ FOCO (Corregido para usar 'activar_foco' y 'desactivar_foco')
        btnEncenderFoco.setOnClickListener {
            pedirConfirmacion("Encender Foco", "¿Encender foco halógeno 35W?", "activar_foco")
        }
        btnApagarFoco.setOnClickListener {
            pedirConfirmacion("Apagar Foco", "¿Apagar foco halógeno?", "desactivar_foco")
        }
    }

    private fun pedirConfirmacion(titulo: String, mensaje: String, accion: String) {
        if (isProcessing) {
            Toast.makeText(requireContext(), "⏳ Procesando comando anterior, espera...", Toast.LENGTH_SHORT).show()
            return
        }

        AlertDialog.Builder(requireContext())
            .setTitle(titulo)
            .setMessage(mensaje)
            .setPositiveButton("Sí, ejecutar") { _, _ ->
                ejecutarComando(accion)
            }
            .setNegativeButton("Cancelar", null)
            .show()
    }

    private fun ejecutarComando(accion: String) {
        isProcessing = true
        setButtonsEnabled(false)

        val comando = Command(
            dispositivoId = 1,
            usuarioId = sessionManager.getUserId(),
            procesoId = 1,
            accion = accion,
            origen = "manual_app"
        )

        lifecycleScope.launch {
            try {
                val response = ApiClient.apiService.sendCommand(comando)

                if (response.isSuccessful && response.body()?.success == true) {
                    Toast.makeText(requireContext(), "✅ Comando enviado", Toast.LENGTH_SHORT).show()

                    // ✅ BLOQUEAR BOTONES POR 70 SEGUNDOS (más que los 60s del ESP32)
                        Handler(Looper.getMainLooper()).postDelayed({
                        isProcessing = false
                        setButtonsEnabled(true)
                    }, 70000) // 70 segundos

                } else {
                    val errorMsg = response.body()?.message ?: "Error en el servidor"
                    Toast.makeText(requireContext(), "❌ $errorMsg", Toast.LENGTH_SHORT).show()
                    isProcessing = false
                    setButtonsEnabled(true)
                }
            } catch (e: Exception) {
                Toast.makeText(requireContext(), "❌ Error: ${e.message}", Toast.LENGTH_SHORT).show()
                isProcessing = false
                setButtonsEnabled(true)
            }
        }
    }

    private fun setButtonsEnabled(enabled: Boolean) {
        val alpha = if (enabled) 1.0f else 0.5f
        btnActivarVent.isEnabled = enabled
        btnDesactivarVent.isEnabled = enabled
        btnEncenderFoco.isEnabled = enabled
        btnApagarFoco.isEnabled = enabled

        btnActivarVent.alpha = alpha
        btnDesactivarVent.alpha = alpha
        btnEncenderFoco.alpha = alpha
        btnApagarFoco.alpha = alpha
    }
}
package com.deshidratador.solar.presentation.monitoring

import android.os.Bundle
import android.os.Handler
import android.os.Looper
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.TextView
import androidx.core.content.ContextCompat
import androidx.fragment.app.Fragment
import androidx.fragment.app.viewModels
import com.deshidratador.solar.R
import com.deshidratador.solar.presentation.viewmodel.MonitoringViewModel
import java.text.SimpleDateFormat
import java.util.Calendar
import java.util.Date
import java.util.Locale

class MonitoringFragment : Fragment() {

    private val viewModel: MonitoringViewModel by viewModels()
    private val handler = Handler(Looper.getMainLooper())
    private var refreshRunnable: Runnable? = null
    private val REFRESH_INTERVAL = 10000L // 10 segundos

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        return inflater.inflate(R.layout.fragment_monitoring, container, false)
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)

        val tvTemp = view.findViewById<TextView>(R.id.tvTemperatura)
        val tvHumedad = view.findViewById<TextView>(R.id.tvHumedad)
        val tvPresion = view.findViewById<TextView>(R.id.tvPresion)
        val tvEstadoConexion = view.findViewById<TextView>(R.id.tvEstadoConexion)
        val tvUltimaActualizacion = view.findViewById<TextView>(R.id.tvUltimaActualizacion)

        // Cargar datos iniciales
        viewModel.loadReadings(limit = 20)

        // Observar la lectura actual
        viewModel.currentReading.observe(viewLifecycleOwner) { reading ->
            reading?.let {
                tvTemp.text = "${it.temperatura} °C"
                tvHumedad.text = "${it.humedad} %"
                tvPresion.text = "${it.presion} hPa"

                // Usar hora LOCAL del dispositivo (más precisa para el PMV)
                val ahora = Calendar.getInstance().time
                val formatoHora = SimpleDateFormat("dd/MM/yyyy HH:mm:ss", Locale.getDefault())
                tvUltimaActualizacion.text = "Última actualización: ${formatoHora.format(ahora)}"
            }
        }

        viewModel.isConnected.observe(viewLifecycleOwner) { isOnline ->
            if (isOnline) {
                tvEstadoConexion.text = "🟢 ESP32: EN LÍNEA"
                tvEstadoConexion.setTextColor(ContextCompat.getColor(requireContext(), android.R.color.holo_green_dark))
            } else {
                tvEstadoConexion.text = "🔴 ESP32: SIN CONEXIÓN (Usando Buffer)"
                tvEstadoConexion.setTextColor(ContextCompat.getColor(requireContext(), android.R.color.holo_red_dark))
            }
        }

        // 🔥 INICIAR REFRESCO AUTOMÁTICO CADA 10 SEGUNDOS
        startAutoRefresh()
    }

    // 🔥 MÉTODO NUEVO: Iniciar refresco automático
    private fun startAutoRefresh() {
        refreshRunnable = object : Runnable {
            override fun run() {
                // Refrescar los datos
                viewModel.loadReadings(limit = 20)

                // Programar el siguiente refresco en 10 segundos
                handler.postDelayed(this, REFRESH_INTERVAL)
            }
        }

        // Iniciar el primer refresco después de 10 segundos
        handler.postDelayed(refreshRunnable!!, REFRESH_INTERVAL)
    }

    // 🔥 MÉTODO NUEVO: Detener refresco cuando se destruye el fragment
    private fun stopAutoRefresh() {
        refreshRunnable?.let { handler.removeCallbacks(it) }
    }

    override fun onPause() {
        super.onPause()
        // Pausar el refresco cuando el usuario sale de la pantalla
        stopAutoRefresh()
    }

    override fun onResume() {
        super.onResume()
        // Reanudar el refresco cuando el usuario regresa
        startAutoRefresh()
    }

    override fun onDestroyView() {
        super.onDestroyView()
        // Limpiar el handler para evitar memory leaks
        stopAutoRefresh()
    }
}
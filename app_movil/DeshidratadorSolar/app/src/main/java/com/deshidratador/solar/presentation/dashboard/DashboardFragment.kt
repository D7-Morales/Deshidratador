package com.deshidratador.solar.presentation.dashboard

import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.TextView
import android.widget.Toast
import androidx.fragment.app.Fragment
import androidx.fragment.app.viewModels
import com.deshidratador.solar.R
import com.deshidratador.solar.data.prefs.SessionManager // <-- 1. AGREGAR ESTE IMPORT
import com.deshidratador.solar.presentation.main.MainActivity
import com.deshidratador.solar.presentation.viewmodel.DashboardViewModel
import com.google.android.material.bottomnavigation.BottomNavigationView
import com.google.android.material.button.MaterialButton

class DashboardFragment : Fragment() {

    private val viewModel: DashboardViewModel by viewModels()
    private lateinit var sessionManager: SessionManager // <-- 2. AGREGAR ESTA VARIABLE

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        return inflater.inflate(R.layout.fragment_dashboard, container, false)
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)

        // 3. INICIALIZAR SESSION MANAGER
        sessionManager = SessionManager(requireContext())

        // 4. MOSTRAR EL NOMBRE REAL DEL USUARIO
        // IMPORTANTE: Cambia "tvBienvenida" por el ID real que tenga tu TextView en el XML
        // (puede ser tvNombre, tvSaludo, etc. Revísalo en tu fragment_dashboard.xml)
        val tvBienvenida = view.findViewById<TextView>(R.id.tvBienvenida)
        val nombreUsuario = sessionManager.getUserNombres()
        tvBienvenida.text = "¡Hola, $nombreUsuario!"

        // 5. Cargar datos al entrar
        viewModel.loadMetrics()

        // 6. Observar las métricas
        viewModel.metrics.observe(viewLifecycleOwner) { metrics ->
            metrics?.let {
                view.findViewById<TextView>(R.id.tvTotalProcesos)?.text = it.totalProcesos.toString()
                view.findViewById<TextView>(R.id.tvPesoInicial)?.text = "${it.pesoInicialPromedio} kg"
                view.findViewById<TextView>(R.id.tvPesoFinal)?.text = "${it.pesoFinalPromedio} kg"
                view.findViewById<TextView>(R.id.tvPorcentajeSecado)?.text = "${it.porcentajeSecadoPromedio}%"
                view.findViewById<TextView>(R.id.tvTiempoTotal)?.text = "${it.tiempoTotalDeshidratacion} hrs"
                view.findViewById<TextView>(R.id.tvTempPromedio)?.text = "${it.temperaturaPromedio}°C"
                view.findViewById<TextView>(R.id.tvHumedadPromedio)?.text = "${it.humedadPromedio}%"
                view.findViewById<TextView>(R.id.tvTotalAlertas)?.text = it.totalAlertas.toString()
                view.findViewById<TextView>(R.id.tvFrutaRecuperada)?.text = "${it.frutaRecuperadaKg} kg"
            }
        }

        // 7. Observar errores
        viewModel.errorMessage.observe(viewLifecycleOwner) { errorMsg ->
            errorMsg?.let {
                Toast.makeText(requireContext(), it, Toast.LENGTH_SHORT).show()
            }
        }

        // 8. Configurar botones de acceso rápido
        val btnMonitoring = view.findViewById<MaterialButton>(R.id.btnMonitoring)
        val btnControl = view.findViewById<MaterialButton>(R.id.btnControl)

        btnMonitoring?.setOnClickListener {
            (requireActivity() as? MainActivity)?.let { activity ->
                activity.findViewById<BottomNavigationView>(R.id.bottomNavigation)?.selectedItemId = R.id.nav_monitoreo
            }
        }

        btnControl?.setOnClickListener {
            (requireActivity() as? MainActivity)?.let { activity ->
                activity.findViewById<BottomNavigationView>(R.id.bottomNavigation)?.selectedItemId = R.id.nav_control
            }
        }
    }
}
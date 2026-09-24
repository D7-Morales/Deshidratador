package com.deshidratador.solar.presentation.main

import android.os.Bundle
import android.util.Log
import androidx.appcompat.app.AppCompatActivity
import androidx.fragment.app.Fragment
import com.deshidratador.solar.R
import com.deshidratador.solar.databinding.ActivityMainBinding
import com.deshidratador.solar.data.prefs.SessionManager
import com.deshidratador.solar.presentation.dashboard.DashboardFragment
import com.deshidratador.solar.presentation.monitoring.MonitoringFragment
import com.deshidratador.solar.presentation.control.ControlFragment
import com.deshidratador.solar.presentation.history.HistoryFragment
import com.deshidratador.solar.presentation.profile.ProfileFragment

class MainActivity : AppCompatActivity() {

    private lateinit var binding: ActivityMainBinding
    private lateinit var sessionManager: SessionManager

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityMainBinding.inflate(layoutInflater)
        setContentView(binding.root)

        // ✅ Inicializar SessionManager para verificar el rol del usuario
        sessionManager = SessionManager(this)

        setupBottomNavigation()

        // Cargar Dashboard por defecto (seguro para cualquier rol)
        if (savedInstanceState == null) {
            loadFragment(DashboardFragment())
        }
    }

    private fun setupBottomNavigation() {
        val menu = binding.bottomNavigation.menu

        // ✅ 1. OBTENER EL ROL DEL USUARIO (Limpiando espacios y sin importar mayúsculas)
        val rol = sessionManager.getUserRole().trim()
        val esAdmin = rol.equals("admin", ignoreCase = true)

        // Log para que puedas verificar en Logcat qué rol está leyendo exactamente
        Log.d("ROLE_CHECK", "Rol detectado en MainActivity: '$rol' | Es Admin: $esAdmin")

        // ✅ 2. RESTRINGIR ACCESOS: Si NO es admin, ocultar Control e Historial
        if (!esAdmin) {
            menu.findItem(R.id.nav_control)?.isVisible = false
            menu.findItem(R.id.nav_historial)?.isVisible = false
        } else {
            // Si es admin, nos aseguramos que sean visibles (por si acaso)
            menu.findItem(R.id.nav_control)?.isVisible = true
            menu.findItem(R.id.nav_historial)?.isVisible = true
        }

        // ✅ 3. Configurar la navegación
        binding.bottomNavigation.setOnItemSelectedListener { item ->
            when (item.itemId) {
                R.id.nav_inicio -> {
                    loadFragment(DashboardFragment())
                    true
                }
                R.id.nav_monitoreo -> {
                    loadFragment(MonitoringFragment())
                    true
                }
                R.id.nav_control -> {
                    loadFragment(ControlFragment())
                    true
                }
                R.id.nav_historial -> {
                    loadFragment(HistoryFragment())
                    true
                }
                R.id.nav_perfil -> {
                    loadFragment(ProfileFragment())
                    true
                }
                else -> false
            }
        }
    }

    private fun loadFragment(fragment: Fragment) {
        supportFragmentManager.beginTransaction()
            .replace(R.id.fragmentContainer, fragment)
            .commit()
    }
}
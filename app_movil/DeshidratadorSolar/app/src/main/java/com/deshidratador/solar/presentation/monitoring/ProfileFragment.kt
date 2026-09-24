package com.deshidratador.solar.presentation.profile

import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Button
import android.widget.TextView
import androidx.appcompat.app.AlertDialog
import androidx.core.content.ContextCompat
import androidx.fragment.app.Fragment
import com.deshidratador.solar.R
import com.deshidratador.solar.data.prefs.SessionManager
import com.google.android.material.chip.Chip

class ProfileFragment : Fragment() {

    private lateinit var sessionManager: SessionManager

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        return inflater.inflate(R.layout.fragment_profile, container, false)
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)

        sessionManager = SessionManager(requireContext())

        // 1. Obtener datos
        val nombres = sessionManager.getUserNombres()
        val apellidos = sessionManager.getUserApellidos()
        val email = sessionManager.getUserEmail()
        val role = sessionManager.getUserRole()

        // 2. Asignar a las vistas
        view.findViewById<TextView>(R.id.tvName).text = "$nombres $apellidos"
        view.findViewById<TextView>(R.id.tvEmail).text = email

        // 3. Mostrar el rol de forma profesional con colores
        val tvRole = view.findViewById<Chip>(R.id.tvRole)
        if (role.equals("admin", ignoreCase = true)) {
            tvRole.text = "Administrador"
            // Color verde (éxito) para el admin
            tvRole.chipBackgroundColor = ContextCompat.getColorStateList(requireContext(), R.color.success)
        } else {
            tvRole.text = "Usuario"
            // Color gris para usuario normal
            tvRole.chipBackgroundColor = ContextCompat.getColorStateList(requireContext(), R.color.medium_gray)
        }

        // 4. Configurar botón de cerrar sesión con confirmación
        view.findViewById<Button>(R.id.btnLogout).setOnClickListener {
            mostrarConfirmacionLogout()
        }
    }

    private fun mostrarConfirmacionLogout() {
        AlertDialog.Builder(requireContext())
            .setTitle("Cerrar Sesión")
            .setMessage("¿Estás seguro de que deseas salir de la aplicación?")
            .setPositiveButton("Sí, salir") { _, _ ->
                sessionManager.logout() // Esto ya redirige y limpia todo
            }
            .setNegativeButton("Cancelar", null)
            .show()
    }
}
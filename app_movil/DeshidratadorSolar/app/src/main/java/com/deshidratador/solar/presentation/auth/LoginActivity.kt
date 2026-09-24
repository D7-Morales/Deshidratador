package com.deshidratador.solar.presentation.auth

import android.content.Intent
import android.os.Bundle
import android.util.Log
import android.view.View
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.lifecycleScope
import com.deshidratador.solar.data.prefs.SessionManager
import com.deshidratador.solar.data.remote.ApiClient
import com.deshidratador.solar.databinding.ActivityLoginBinding
import com.deshidratador.solar.presentation.main.MainActivity
import kotlinx.coroutines.delay
import kotlinx.coroutines.launch

class LoginActivity : AppCompatActivity() {

    private lateinit var binding: ActivityLoginBinding
    private lateinit var sessionManager: SessionManager

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityLoginBinding.inflate(layoutInflater)
        setContentView(binding.root)

        sessionManager = SessionManager(this)

        // Si ya está logueado, ir directo al MainActivity
        if (sessionManager.isLoggedIn()) {
            startActivity(Intent(this, MainActivity::class.java))
            finish()
        }

        setupListeners()
    }

    private fun setupListeners() {
        binding.btnLogin.setOnClickListener {
            val email = binding.etEmail.text.toString().trim()
            val password = binding.etPassword.text.toString().trim()

            if (email.isEmpty() || password.isEmpty()) {
                Toast.makeText(this, "Completa todos los campos", Toast.LENGTH_SHORT).show()
                return@setOnClickListener
            }

            performLogin(email, password)
        }
    }

    private fun performLogin(email: String, password: String) {
        binding.btnLogin.isEnabled = false
        binding.progressBar.visibility = View.VISIBLE

        lifecycleScope.launch {
            try {
                val credentials = mapOf("email" to email, "password" to password)
                val response = ApiClient.apiService.login(credentials)

                Log.d("LOGIN_DEBUG", "Response code: ${response.code()}")
                Log.d("LOGIN_DEBUG", "Response body: ${response.body()}")

                if (response.isSuccessful && response.body()?.success == true) {

                    val userData = response.body()!!.data

                    // ✅ VERIFICAR QUE LOS DATOS NO SEAN NULL
                    Log.d("LOGIN_DEBUG", "ID Usuario: ${userData.id_usuario}")
                    Log.d("LOGIN_DEBUG", "Nombres: ${userData.nombres_usuario}")
                    Log.d("LOGIN_DEBUG", "Rol: ${userData.rol?.nombre_rol}")
                    Log.d("LOGIN_DEBUG", "Token: ${userData.token}")

                    // ✅ GUARDAR SESIÓN DE FORMA EXPLÍCITA
                    sessionManager.saveLogin(
                        userId = userData.id_usuario,
                        nombres = userData.nombres_usuario ?: "Usuario",
                        apellidos = userData.apellidos_usuario ?: "",
                        email = userData.email_usuario ?: email,
                        role = userData.rol?.nombre_rol ?: "user",
                        token = userData.token ?: ""
                    )

                    // ✅ VERIFICAR QUE SE GUARDÓ CORRECTAMENTE
                    Log.d("LOGIN_DEBUG", "isLoggedIn: ${sessionManager.isLoggedIn()}")
                    Log.d("LOGIN_DEBUG", "UserRole: ${sessionManager.getUserRole()}")
                    Log.d("LOGIN_DEBUG", "Token: ${sessionManager.getToken()}")

                    // ✅ PEQUEÑA PAUSA PARA ASEGURAR PERSISTENCIA
                    delay(100)

                    Toast.makeText(
                        this@LoginActivity,
                        "Bienvenido ${userData.nombres_usuario}",
                        Toast.LENGTH_SHORT
                    ).show()

                    // ✅ IR A MainActivity Y LIMPIAR BACK STACK
                    val intent = Intent(this@LoginActivity, MainActivity::class.java)
                    intent.flags = Intent.FLAG_ACTIVITY_NEW_TASK or Intent.FLAG_ACTIVITY_CLEAR_TASK
                    startActivity(intent)
                    finish()

                } else {
                    val errorMsg = response.body()?.message ?: "Credenciales incorrectas"
                    Log.e("LOGIN_ERROR", "Error: $errorMsg")
                    Toast.makeText(this@LoginActivity, errorMsg, Toast.LENGTH_SHORT).show()
                }
            } catch (e: Exception) {
                Log.e("LOGIN_EXCEPTION", "Excepción: ${e.message}", e)
                e.printStackTrace()
                Toast.makeText(
                    this@LoginActivity,
                    "Error de conexión: ${e.message}",
                    Toast.LENGTH_LONG
                ).show()
            } finally {
                binding.btnLogin.isEnabled = true
                binding.progressBar.visibility = View.GONE
            }
        }
    }
}
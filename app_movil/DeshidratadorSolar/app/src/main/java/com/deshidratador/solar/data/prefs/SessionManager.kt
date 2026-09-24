package com.deshidratador.solar.data.prefs

import android.content.Context
import android.content.Intent
import android.content.SharedPreferences
import com.deshidratador.solar.presentation.auth.LoginActivity

class SessionManager(context: Context) {

    // Usamos applicationContext para evitar memory leaks
    private val context: Context = context.applicationContext
    private val prefs: SharedPreferences = context.getSharedPreferences(
        "deshidratador_prefs", Context.MODE_PRIVATE
    )

    companion object {
        private const val KEY_IS_LOGGED = "is_logged"
        private const val KEY_USER_ID = "user_id"
        private const val KEY_USER_NOMBRES = "user_nombres"
        private const val KEY_USER_APELLIDOS = "user_apellidos"
        private const val KEY_USER_EMAIL = "user_email"
        private const val KEY_USER_ROLE = "user_role"
        private const val KEY_TOKEN = "token"
    }

    fun saveLogin(
        userId: Long,
        nombres: String,
        apellidos: String,
        email: String,
        role: String,
        token: String
    ) {
        prefs.edit()
            .putBoolean(KEY_IS_LOGGED, true)
            .putLong(KEY_USER_ID, userId)
            .putString(KEY_USER_NOMBRES, nombres)
            .putString(KEY_USER_APELLIDOS, apellidos)
            .putString(KEY_USER_EMAIL, email)
            .putString(KEY_USER_ROLE, role)
            .putString(KEY_TOKEN, token)
            .apply()
    }

    fun isLoggedIn(): Boolean = prefs.getBoolean(KEY_IS_LOGGED, false)
    fun getUserId(): Long = prefs.getLong(KEY_USER_ID, -1)
    fun getUserNombres(): String = prefs.getString(KEY_USER_NOMBRES, "") ?: ""
    fun getUserApellidos(): String = prefs.getString(KEY_USER_APELLIDOS, "") ?: ""
    fun getUserEmail(): String = prefs.getString(KEY_USER_EMAIL, "") ?: ""
    fun getUserRole(): String = prefs.getString(KEY_USER_ROLE, "") ?: ""
    fun getToken(): String = prefs.getString(KEY_TOKEN, "") ?: ""

    /**
     * Cierra la sesión, borra los datos y redirige al Login limpiando el historial.
     */
    fun logout() {
        prefs.edit().clear().apply()

        // Redirigir al Login y destruir todas las actividades anteriores
        val intent = Intent(context, LoginActivity::class.java)
        intent.flags = Intent.FLAG_ACTIVITY_NEW_TASK or Intent.FLAG_ACTIVITY_CLEAR_TASK
        context.startActivity(intent)
    }
}
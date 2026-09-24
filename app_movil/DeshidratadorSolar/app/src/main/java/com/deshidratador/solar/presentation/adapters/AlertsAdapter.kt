package com.deshidratador.solar.presentation.adapters

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Button
import android.widget.TextView
import android.widget.Toast
import androidx.core.content.ContextCompat
import androidx.lifecycle.lifecycleScope
import androidx.recyclerview.widget.RecyclerView
import com.deshidratador.solar.R
import com.deshidratador.solar.data.model.AlertsResponse
import com.deshidratador.solar.data.remote.ApiClient
import kotlinx.coroutines.launch

class AlertsAdapter(
    private val alertList: List<AlertsResponse>,
    private val onAlertaAtendida: () -> Unit
) : RecyclerView.Adapter<AlertsAdapter.AlertViewHolder>() {

    class AlertViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        val tvTipo: TextView = view.findViewById(R.id.tvAlertType)
        val tvMensaje: TextView = view.findViewById(R.id.tvAlertMessage)
        val tvFecha: TextView = view.findViewById(R.id.tvAlertDate)
        val tvEstado: TextView = view.findViewById(R.id.tvAlertStatus)
        val btnAtender: Button = view.findViewById(R.id.btnAtenderAlerta)
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): AlertViewHolder {
        val view = LayoutInflater.from(parent.context)
            .inflate(R.layout.item_alert, parent, false)
        return AlertViewHolder(view)
    }

    override fun onBindViewHolder(holder: AlertViewHolder, position: Int) {
        val alert = alertList[position]

        holder.tvTipo.text = alert.tipo.replace("_", " ").uppercase()
        holder.tvMensaje.text = alert.mensaje
        holder.tvFecha.text = alert.getFechaFormateada()
        holder.tvEstado.text = if (alert.atendida) "Atendida" else "Pendiente"

        // Color según tipo
        if (alert.esCritica()) {
            holder.tvTipo.setTextColor(ContextCompat.getColor(holder.itemView.context, android.R.color.holo_red_dark))
        } else {
            holder.tvTipo.setTextColor(ContextCompat.getColor(holder.itemView.context, android.R.color.holo_blue_dark))
        }

        // Mostrar/ocultar botón según estado
        if (alert.atendida) {
            holder.btnAtender.visibility = View.GONE
            holder.tvEstado.setTextColor(ContextCompat.getColor(holder.itemView.context, android.R.color.holo_green_dark))
        } else {
            holder.btnAtender.visibility = View.VISIBLE
            holder.tvEstado.setTextColor(ContextCompat.getColor(holder.itemView.context, android.R.color.holo_orange_dark))

            holder.btnAtender.setOnClickListener {
                val context = holder.itemView.context
                if (context is androidx.fragment.app.FragmentActivity) {
                    context.lifecycleScope.launch {
                        try {
                            val response = ApiClient.apiService.marcarAlertaAtendida(alert.id)
                            if (response.isSuccessful && response.body()?.get("success") == true) {
                                Toast.makeText(context, "✅ Alerta atendida", Toast.LENGTH_SHORT).show()
                                onAlertaAtendida()
                            } else {
                                Toast.makeText(context, "❌ Error al atender", Toast.LENGTH_SHORT).show()
                            }
                        } catch (e: Exception) {
                            Toast.makeText(context, "❌ Error: ${e.message}", Toast.LENGTH_SHORT).show()
                        }
                    }
                }
            }
        }
    }

    override fun getItemCount(): Int = alertList.size
}
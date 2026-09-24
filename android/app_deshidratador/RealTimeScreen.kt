package com.example.deshidratadorsolar

import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Info
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp

@Composable
fun RealTimeScreen(response: MetricsResponse?) {
    if (response == null) {
        Box(
            modifier = Modifier.fillMaxSize(),
            contentAlignment = Alignment.Center
        ) {
            CircularProgressIndicator(color = MaterialTheme.colorScheme.primary)
        }
        return
    }

    val metrics = response.metrics
    val activeProceso = response.procesoActivo
    val reading = response.ultimaLectura

    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(Color(0xFFF4F6F9))
            .padding(16.dp)
            .verticalScroll(rememberScrollState())
    ) {
        // Header
        Text(
            text = "Monitoreo en Tiempo Real",
            fontSize = 22.sp,
            fontWeight = FontWeight.Bold,
            color = Color(0xFF1E293B)
        )
        Spacer(modifier = Modifier.height(4.dp))
        Text(
            text = "Mercado La Pampa - Cochabamba",
            fontSize = 14.sp,
            color = Color.Gray
        )

        Spacer(modifier = Modifier.height(16.dp))

        // Connection status card
        Card(
            shape = RoundedCornerShape(12.dp),
            colors = CardDefaults.cardColors(containerColor = Color.White),
            elevation = CardDefaults.cardElevation(defaultElevation = 2.dp),
            modifier = Modifier.fillMaxWidth()
        ) {
            Row(
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(16.dp),
                verticalAlignment = Alignment.CenterVertically,
                horizontalArrangement = Arrangement.SpaceBetween
            ) {
                Column {
                    Text(text = "Estado del ESP32", fontWeight = FontWeight.Bold, color = Color(0xFF475569))
                    Text(
                        text = "Ult. Conexión: ${metrics?.ultimaConexion ?: "N/A"}",
                        fontSize = 12.sp,
                        color = Color.Gray
                    )
                }
                val onlineColor = if (metrics?.isOnline == true) Color(0xFF10B981) else Color(0xFFEF4444)
                val onlineText = if (metrics?.isOnline == true) "ONLINE" else "OFFLINE"
                
                Surface(
                    shape = RoundedCornerShape(20.dp),
                    color = onlineColor.copy(alpha = 0.15f),
                    modifier = Modifier.padding(horizontal = 8.dp)
                ) {
                    Text(
                        text = onlineText,
                        color = onlineColor,
                        fontWeight = FontWeight.Bold,
                        fontSize = 13.sp,
                        modifier = Modifier.padding(horizontal = 12.dp, vertical = 6.dp)
                    )
                }
            }
        }

        Spacer(modifier = Modifier.height(16.dp))

        // Active Process Details
        if (activeProceso != null) {
            Card(
                shape = RoundedCornerShape(12.dp),
                colors = CardDefaults.cardColors(containerColor = Color(0xFFFFFBEB)),
                modifier = Modifier.fillMaxWidth()
            ) {
                Column(modifier = Modifier.padding(16.dp)) {
                    Text(
                        text = "PROCESO ACTIVO",
                        fontWeight = FontWeight.Bold,
                        color = Color(0xFFD97706),
                        fontSize = 12.sp
                    )
                    Spacer(modifier = Modifier.height(4.dp))
                    Text(
                        text = activeProceso.nombreFruta,
                        fontWeight = FontWeight.Bold,
                        fontSize = 18.sp,
                        color = Color(0xFF1E293B)
                    )
                    Spacer(modifier = Modifier.height(4.dp))
                    Text(text = "Lote: ${activeProceso.lote} | Bandeja: ${activeProceso.bandeja}", fontSize = 14.sp)
                }
            }
        } else {
            Card(
                shape = RoundedCornerShape(12.dp),
                colors = CardDefaults.cardColors(containerColor = Color(0xFFF1F5F9)),
                modifier = Modifier.fillMaxWidth()
            ) {
                Row(
                    modifier = Modifier.padding(16.dp),
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    Icon(imageVector = Icons.Default.Info, contentDescription = null, tint = Color.Gray)
                    Spacer(modifier = Modifier.width(8.dp))
                    Text(text = "Sin procesos de deshidratación activos", color = Color.Gray, fontSize = 14.dp.value.sp)
                }
            }
        }

        Spacer(modifier = Modifier.height(16.dp))

        // Instant metrics
        if (reading != null) {
            Row(modifier = Modifier.fillMaxWidth()) {
                Card(
                    modifier = Modifier.weight(1f).padding(end = 6.dp),
                    colors = CardDefaults.cardColors(containerColor = Color(0xFFFEF3C7))
                ) {
                    Column(modifier = Modifier.padding(12.dp)) {
                        Text(text = "Temp. Instantánea", fontSize = 11.sp, color = Color(0xFF92400E))
                        Text(text = "${reading.temperatura} °C", fontSize = 20.sp, fontWeight = FontWeight.Bold, color = Color(0xFF92400E))
                    }
                }
                Card(
                    modifier = Modifier.weight(1f).padding(start = 6.dp),
                    colors = CardDefaults.cardColors(containerColor = Color(0xFFE0F2FE))
                ) {
                    Column(modifier = Modifier.padding(12.dp)) {
                        Text(text = "Hum. Instantánea", fontSize = 11.sp, color = Color(0xFF075985))
                        Text(text = "${reading.humedad} %", fontSize = 20.sp, fontWeight = FontWeight.Bold, color = Color(0xFF075985))
                    }
                }
            }
        }

        Spacer(modifier = Modifier.height(16.dp))

        // 7 Metrics Cards Grid
        if (metrics != null) {
            Text(
                text = "Métricas Clave",
                fontSize = 16.sp,
                fontWeight = FontWeight.Bold,
                color = Color(0xFF334155),
                modifier = Modifier.padding(bottom = 8.dp)
            )

            val items = listOf(
                MetricItem("Procesos Totales", "${metrics.totalProcesos}", Color(0xFF3B82F6)),
                MetricItem("Secado Promedio", "${metrics.porcentajeSecadoPromedio}%", Color(0xFF10B981)),
                MetricItem("Tiempo Acumulado", "${metrics.tiempoTotalHoras} Hrs", Color(0xFFF59E0B)),
                MetricItem("Fruta Recuperada", "${metrics.frutaRecuperadaKg} kg", Color(0xFF8B5CF6)),
                MetricItem("Peso Inicial Total", "${metrics.pesoInicialTotalKg} kg", Color(0xFF64748B)),
                MetricItem("Peso Final Total", "${metrics.pesoFinalTotalKg} kg", Color(0xFF0EA5E9)),
                MetricItem("Alertas Totales", "${metrics.totalAlertas}", Color(0xFFEF4444))
            )

            Column {
                for (i in items.indices step 2) {
                    Row(modifier = Modifier.fillMaxWidth().padding(bottom = 12.dp)) {
                        Box(modifier = Modifier.weight(1f).padding(end = 6.dp)) {
                            MetricCard(items[i])
                        }
                        Box(modifier = Modifier.weight(1f).padding(start = 6.dp)) {
                            if (i + 1 < items.size) {
                                MetricCard(items[i + 1])
                            } else {
                                Spacer(modifier = Modifier.fillMaxWidth())
                            }
                        }
                    }
                }
            }
        }
    }
}

data class MetricItem(val label: String, val value: String, val color: Color)

@Composable
fun MetricCard(item: MetricItem) {
    Card(
        shape = RoundedCornerShape(12.dp),
        colors = CardDefaults.cardColors(containerColor = Color.White),
        elevation = CardDefaults.cardElevation(defaultElevation = 2.dp),
        modifier = Modifier.fillMaxWidth()
    ) {
        Column(modifier = Modifier.padding(14.dp)) {
            Text(text = item.label, fontSize = 12.sp, color = Color.Gray, fontWeight = FontWeight.Medium)
            Spacer(modifier = Modifier.height(4.dp))
            Text(text = item.value, fontSize = 18.sp, fontWeight = FontWeight.Bold, color = item.color)
        }
    }
}

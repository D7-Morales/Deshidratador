package com.example.deshidratadorsolar

import android.os.Bundle
import androidx.activity.ComponentActivity
import androidx.activity.compose.setContent
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.padding
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Info
import androidx.compose.material.icons.filled.Warning
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Modifier
import androidx.lifecycle.lifecycleScope
import com.example.deshidratadorsolar.ui.theme.DeshidratadorSolarTheme
import kotlinx.coroutines.delay
import kotlinx.coroutines.launch

class MainActivity : ComponentActivity() {

    private val apiService by lazy { ApiService.create() }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        
        setContent {
            var selectedTab by remember { mutableStateOf(0) }
            var metricsResponse by remember { mutableStateOf<MetricsResponse?>(null) }
            var alertsResponse by remember { mutableStateOf<AlertsResponse?>(null) }

            // Polling loop to fetch data from API automatically every 10 seconds
            LaunchedEffect(Unit) {
                lifecycleScope.launch {
                    while (true) {
                        try {
                            metricsResponse = apiService.getMetrics()
                            alertsResponse = apiService.getAlerts()
                        } catch (e: Exception) {
                            e.printStackTrace()
                            // Aquí podrías manejar fallas de conexión o de red local
                        }
                        delay(10000) // Poll every 10 seconds
                    }
                }
            }

            MaterialTheme {
                Scaffold(
                    bottomBar = {
                        NavigationBar {
                            NavigationBarItem(
                                icon = { Icon(Icons.Default.Info, contentDescription = "Tiempo Real") },
                                label = { Text("Tiempo Real") },
                                selected = selectedTab == 0,
                                onClick = { selectedTab = 0 }
                            )
                            NavigationBarItem(
                                icon = { Icon(Icons.Default.Warning, contentDescription = "Alertas") },
                                label = { Text("Alertas") },
                                selected = selectedTab == 1,
                                onClick = { selectedTab = 1 }
                            )
                        }
                    }
                ) { innerPadding ->
                    Box(modifier = Modifier.fillMaxSize().padding(innerPadding)) {
                        when (selectedTab) {
                            0 -> RealTimeScreen(response = metricsResponse)
                            1 -> AlertsScreen(response = alertsResponse)
                        }
                    }
                }
            }
        }
    }
}

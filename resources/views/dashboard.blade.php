@extends('layouts.app')

@section('title', 'Dashboard Principal')
@section('page_title', 'Panel de Monitoreo Inteligente')

@section('content')
<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <p class="text-muted mb-0">Monitoreo y métricas de deshidratación solar - Mercado La Pampa.</p>
            @if($procesoActivo)
                <div class="mt-2">
                    <span class="badge badge-warning px-3 py-2 text-dark font-weight-bold" style="border-radius: 12px; font-size: 13px;">
                        <i class="fas fa-spinner fa-spin mr-1"></i> Proceso Activo: <strong>{{ $procesoActivo->fruta->nombre_fruta }}</strong> (Lote: {{ $procesoActivo->numero_lote }})
                    </span>
                </div>
            @else
                <div class="mt-2">
                    <span class="badge badge-secondary px-3 py-2 font-weight-bold" style="border-radius: 12px; font-size: 13px;">
                        <i class="fas fa-info-circle mr-1"></i> Sin procesos activos en este momento
                    </span>
                </div>
            @endif
        </div>
        <div class="d-flex align-items-center flex-wrap mt-2 mt-md-0">
            <!-- Connection Indicator -->
            <div class="mr-4 d-flex align-items-center">
                <span class="font-weight-bold mr-2 text-dark">ESP32 Status:</span>
                <span id="statusIndicator" class="badge {{ $isOnline ? 'badge-success' : 'badge-danger' }} px-3 py-2 font-weight-bold d-flex align-items-center" style="border-radius: 20px; font-size: 13px;">
                    <i class="fas fa-circle mr-2" id="statusDot" style="font-size: 8px;"></i>
                    <span id="statusText">{{ $isOnline ? 'ONLINE' : 'OFFLINE' }}</span>
                </span>
            </div>
            
            <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input" id="autoRefreshToggle" checked>
                <label class="custom-control-label font-weight-bold text-dark" for="autoRefreshToggle">
                    <i class="fas fa-sync fa-spin text-info mr-1" id="refreshIcon"></i> Refresco Automático (10s)
                </label>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- NUEVA SECCIÓN: ESTADO DEL VENTILADOR -->
<!-- ========================================== -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0" style="border-radius: 16px;">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-3 text-center">
                        <div class="rounded-circle bg-light p-4 d-inline-block">
                            <i class="fas fa-fan fa-3x {{ $estadoVentilador === 'ACTIVADO' ? 'text-danger' : 'text-success' }} {{ $estadoVentilador === 'ACTIVADO' ? 'fan-spinning' : '' }}"></i>                        </div>
                    </div>
                    <div class="col-md-9">
                        <h5 class="text-muted text-uppercase mb-2">Estado del Ventilador</h5>
                        <h2 class="font-weight-bold mb-2">
                            @if($estadoVentilador === 'ACTIVADO')
                                <span class="text-danger"><i class="fas fa-power-off mr-2"></i>ACTIVADO</span>
                            @else
                                <span class="text-success"><i class="fas fa-pause mr-2"></i>DESACTIVADO</span>
                            @endif
                        </h2>
                        <p class="text-muted mb-0"><i class="fas fa-info-circle mr-1"></i> {{ $razonVentilador }}</p>
                        <small class="text-muted">Umbral de activación: Temp > 22°C o Humedad >= 50%</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Metrics Grid (Tutor Requirement #3) -->
<div class="row">
    <!-- 1. Cantidad de procesos -->
    <div class="col-lg-3 col-md-6 col-12 mb-4">
        <div class="card h-100 bg-info text-white metric-card">
            <div class="card-body">
                <p class="text-uppercase text-xs font-weight-bold opacity-8 mb-1">Cantidad de Procesos</p>
                <h2 class="font-weight-bold mb-0" id="metricTotalProcesos">{{ $totalProcesos }}</h2>
                <i class="fas fa-sync-alt position-absolute" style="right: 20px; bottom: 20px; font-size: 40px; opacity: 0.15;"></i>
            </div>
        </div>
    </div>

    <!-- 3. % de secado promedio -->
    <div class="col-lg-3 col-md-6 col-12 mb-4">
        <div class="card h-100 bg-success text-white metric-card">
            <div class="card-body">
                <p class="text-uppercase text-xs font-weight-bold opacity-8 mb-1">% de Secado Promedio</p>
                <h2 class="font-weight-bold mb-0"><span id="metricPorcentajeSecado">{{ number_format($porcentajeSecadoPromedio, 1) }}</span>%</h2>
                <i class="fas fa-percent position-absolute" style="right: 20px; bottom: 20px; font-size: 40px; opacity: 0.15;"></i>
            </div>
        </div>
    </div>

    <!-- 4. Tiempo total acumulado -->
    <div class="col-lg-3 col-md-6 col-12 mb-4">
        <div class="card h-100 bg-warning text-white metric-card">
            <div class="card-body">
                <p class="text-uppercase text-xs font-weight-bold opacity-8 mb-1">Tiempo de Deshidratado</p>
                <h2 class="font-weight-bold mb-0"><span id="metricTiempoTotal">{{ number_format($tiempoTotalHoras, 1) }}</span> <small style="font-size: 16px;">Hrs</small></h2>
                <i class="fas fa-hourglass-half position-absolute" style="right: 20px; bottom: 20px; font-size: 40px; opacity: 0.15;"></i>
            </div>
        </div>
    </div>

    <!-- 7. Fruta recuperada -->
    <div class="col-lg-3 col-md-6 col-12 mb-4">
        <div class="card h-100 bg-primary text-white metric-card">
            <div class="card-body">
                <p class="text-uppercase text-xs font-weight-bold opacity-8 mb-1">Fruta Recuperada</p>
                <h2 class="font-weight-bold mb-0"><span id="metricFrutaRecuperada">{{ number_format($frutaRecuperadaKg, 2) }}</span> <small style="font-size: 16px;">kg</small></h2>
                <i class="fas fa-leaf position-absolute" style="right: 20px; bottom: 20px; font-size: 40px; opacity: 0.15;"></i>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- 2. Peso inicial / final total -->
    <div class="col-lg-4 col-md-6 col-12 mb-4">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle bg-light p-3 mr-3 text-info">
                    <i class="fas fa-weight fa-2x"></i>
                </div>
                <div>
                    <span class="text-xs text-muted text-uppercase d-block">Peso Inicial / Final Total</span>
                    <h5 class="font-weight-bold mb-0 text-dark">
                        <span id="metricPesoInicial">{{ number_format($pesoInicialTotalKg, 2) }}</span> kg / 
                        <span id="metricPesoFinal">{{ number_format($pesoFinalTotalKg, 2) }}</span> kg
                    </h5>
                </div>
            </div>
        </div>
    </div>

    <!-- 5. Temp/Hum promedio -->
    <div class="col-lg-4 col-md-6 col-12 mb-4">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle bg-light p-3 mr-3 text-warning">
                    <i class="fas fa-chart-bar fa-2x"></i>
                </div>
                <div>
                    <span class="text-xs text-muted text-uppercase d-block">Temp / Hum Promedio</span>
                    <h5 class="font-weight-bold mb-0 text-dark">
                        <span id="metricTempPromedio">{{ number_format($tempPromedio, 1) }}</span>°C / 
                        <span id="metricHumPromedio">{{ number_format($humPromedio, 1) }}</span>%
                    </h5>
                </div>
            </div>
        </div>
    </div>

    <!-- 6. Número de alertas -->
    <div class="col-lg-4 col-md-6 col-12 mb-4">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle bg-light p-3 mr-3 text-danger">
                    <i class="fas fa-exclamation-triangle fa-2x"></i>
                </div>
                <div>
                    <span class="text-xs text-muted text-uppercase d-block">Alertas Registradas</span>
                    <h5 class="font-weight-bold mb-0 text-danger" id="metricTotalAlertas">{{ $totalAlertas }}</h5>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- NUEVA SECCIÓN: PROCESO ACTIVO DETALLADO -->
<!-- ========================================== -->
@if($procesoActivoDetalles)
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0 bg-gradient-light" style="border-radius: 16px; border: 2px solid #ffc107;">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="card-title font-weight-bold text-dark mb-0">
                    <i class="fas fa-spinner fa-spin text-warning mr-2"></i>Proceso de Deshidratación Activo
                </h5>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="row">
                    <div class="col-md-3 col-6 mb-3">
                        <span class="text-muted text-xs d-block">Lote</span>
                        <h6 class="font-weight-bold mb-0">{{ $procesoActivoDetalles['numero_lote'] }}</h6>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <span class="text-muted text-xs d-block">Fruta</span>
                        <h6 class="font-weight-bold mb-0">{{ $procesoActivoDetalles['fruta'] }}</h6>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <span class="text-muted text-xs d-block">Tiempo Transcurrido</span>
                        <h6 class="font-weight-bold mb-0">{{ $procesoActivoDetalles['tiempo_transcurrido_horas'] }} hrs</h6>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <span class="text-muted text-xs d-block">% Secado</span>
                        <h6 class="font-weight-bold mb-0 text-warning">{{ $procesoActivoDetalles['porcentaje_secado'] }}%</h6>
                    </div>
                </div>
                <div class="row mt-3 pt-3 border-top">
                    <div class="col-md-6">
                        <span class="text-muted text-xs d-block">Peso Inicial</span>
                        <h6 class="font-weight-bold mb-0">{{ $procesoActivoDetalles['peso_inicial_kg'] }} kg</h6>
                    </div>
                    <div class="col-md-6">
                        <span class="text-muted text-xs d-block">Peso Actual</span>
                        <h6 class="font-weight-bold mb-0">{{ $procesoActivoDetalles['peso_actual_kg'] }} kg</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Instant Metrics Row -->
<div class="row">
    <div class="col-md-4 col-12 mb-4">
        <div class="card bg-gradient-warning text-dark shadow-sm border-0 p-3 h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-xs text-uppercase font-weight-bold opacity-8">Temperatura Instantánea</span>
                    <h3 class="font-weight-bold mb-0 mt-1"><span id="instTemp">{{ $ultimaLectura ? number_format($ultimaLectura->temperatura, 1) : 'N/A' }}</span> °C</h3>
                </div>
                <i class="fas fa-thermometer-half fa-3x opacity-4"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-12 mb-4">
        <div class="card bg-gradient-info text-white shadow-sm border-0 p-3 h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-xs text-uppercase font-weight-bold opacity-8">Humedad Instantánea</span>
                    <h3 class="font-weight-bold mb-0 mt-1"><span id="instHum">{{ $ultimaLectura ? number_format($ultimaLectura->humedad, 1) : 'N/A' }}</span> %</h3>
                </div>
                <i class="fas fa-tint fa-3x opacity-4"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-12 mb-4">
        <div class="card bg-gradient-success text-white shadow-sm border-0 p-3 h-100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-xs text-uppercase font-weight-bold opacity-8">Última Actualización</span>
                    <h5 class="font-weight-bold mb-0 mt-2" id="instTime" style="font-size: 17px;">
                        {{ $ultimaLectura ? $ultimaLectura->fecha_hora->format('d/m/Y H:i:s') : 'Sin registros' }}
                    </h5>
                </div>
                <i class="far fa-clock fa-3x opacity-4"></i>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- NUEVA SECCIÓN: ÚLTIMAS ALERTAS -->
<!-- ========================================== -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0" style="border-radius: 16px;">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="card-title font-weight-bold text-dark mb-0">
                    <i class="fas fa-exclamation-triangle text-danger mr-2"></i>Últimas Alertas
                </h5>
            </div>
            <div class="card-body px-4 pb-4">
                @if($ultimasAlertas->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Fecha/Hora</th>
                                    <th>Tipo</th>
                                    <th>Mensaje</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ultimasAlertas as $alerta)
                                    <tr>
                                        <td>{{ $alerta->fecha_alerta->format('d/m/Y H:i') }}</td>
                                        <td>
                                            @if($alerta->tipo_alerta === 'temperatura')
                                                <span class="badge badge-danger"><i class="fas fa-thermometer-half mr-1"></i>Temperatura</span>
                                            @elseif($alerta->tipo_alerta === 'humedad')
                                                <span class="badge badge-info"><i class="fas fa-tint mr-1"></i>Humedad</span>
                                            @else
                                                <span class="badge badge-warning"><i class="fas fa-exclamation mr-1"></i>Sistema</span>
                                            @endif
                                        </td>
                                        <td>{{ $alerta->mensaje }}</td>
                                        <td>
                                            @if($alerta->atendida)
                                                <span class="badge badge-success">Atendida</span>
                                            @else
                                                <span class="badge badge-warning">Pendiente</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                        <p class="text-muted mb-0">No hay alertas registradas. ¡Todo funciona correctamente!</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Live chart -->
    <div class="col-lg-6 col-12 mb-4">
        <div class="card shadow-sm border-0 h-100" style="border-radius: 16px;">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="card-title font-weight-bold text-dark mb-0">
                    <i class="fas fa-chart-line text-warning mr-2"></i>Historial Climatológico Reciente
                </h5>
            </div>
            <div class="card-body px-4 pb-4">
                <div style="height: 300px; position: relative;">
                    <canvas id="liveSensorChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Last Readings Table -->
    <div class="col-lg-6 col-12 mb-4">
        <div class="card shadow-sm border-0 h-100" style="border-radius: 16px;">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="card-title font-weight-bold text-dark mb-0">
                    <i class="fas fa-history text-warning mr-2"></i>Últimas 10 Lecturas
                </h5>
            </div>
            <div class="card-body p-0 table-responsive">
                <table class="table table-hover table-striped mb-0 text-center">
                    <thead class="bg-light text-muted">
                        <tr>
                            <th>ID</th>
                            <th>Temperatura (°C)</th>
                            <th>Humedad (%)</th>
                            <th>Presión (hPa)</th>
                            <th>Fecha y Hora</th>
                        </tr>
                    </thead>
                    <tbody id="readingsTableBody">
                        @forelse($ultimasLecturas as $lectura)
                            <tr>
                                <td class="font-weight-bold text-muted">#{{ $lectura->id_lectura }}</td>
                                <td>
                                    <span class="badge badge-warning px-2 py-1 font-weight-bold text-dark">
                                        {{ number_format($lectura->temperatura, 2) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-info px-2 py-1 font-weight-bold">
                                        {{ number_format($lectura->humedad, 2) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-success px-2 py-1 font-weight-bold">
                                        {{ number_format($lectura->presion, 2) }}
                                    </span>
                                </td>
                                <td class="text-secondary font-weight-bold">
                                    {{ $lectura->fecha_hora->format('d/m/Y H:i:s') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-5 text-muted">No hay lecturas registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    $(document).ready(function() {
        let refreshInterval = null;

        // Initialize Chart.js
        const ctx = document.getElementById('liveSensorChart').getContext('2d');
        
        // Prepare initial data from backend variables
        const initialLabels = [];
        const initialTemp = [];
        const initialHum = [];

        @foreach(array_reverse($ultimasLecturas->toArray()) as $l)
            initialLabels.push('{{ \Carbon\Carbon::parse($l['fecha_hora'])->format('H:i:s') }}');
            initialTemp.push({{ $l['temperatura'] }});
            initialHum.push({{ $l['humedad'] }});
        @endforeach

        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: initialLabels,
                datasets: [
                    {
                        label: 'Temperatura (°C)',
                        data: initialTemp,
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245, 158, 11, 0.1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Humedad (%)',
                        data: initialHum,
                        borderColor: '#0ea5e9',
                        backgroundColor: 'rgba(14, 165, 233, 0.1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: false
                    }
                }
            }
        });

        // Function to fetch metrics and reload chart/cards asynchronously (AJAX)
        function fetchMetrics() {
            $.ajax({
                url: '{{ url("/api/metrics") }}',
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        const m = response.metrics;
                        const l = response.ultima_lectura;

                        // 1. Update metric cards
                        $('#metricTotalProcesos').text(m.total_procesos);
                        $('#metricPorcentajeSecado').text(m.porcentaje_secado_promedio.toFixed(1));
                        $('#metricTiempoTotal').text(m.tiempo_total_horas.toFixed(1));
                        $('#metricFrutaRecuperada').text(m.fruta_recuperada_kg.toFixed(2));
                        
                        $('#metricPesoInicial').text(m.peso_inicial_total_kg.toFixed(2));
                        $('#metricPesoFinal').text(m.peso_final_total_kg.toFixed(2));
                        $('#metricTempPromedio').text(m.temperatura_promedio.toFixed(1));
                        $('#metricHumPromedio').text(m.humedad_promedio.toFixed(1));
                        $('#metricTotalAlertas').text(m.total_alertas);

                        // 2. Update instant cards
                        if (l) {
                            $('#instTemp').text(l.temperatura.toFixed(1));
                            $('#instHum').text(l.humedad.toFixed(1));
                            $('#instTime').text(l.fecha_hora);
                        }

                        // 3. Update status indicator (Green/Red dot - Tutor requirement #4)
                        if (m.is_online) {
                            // 4. Update Fan Status dynamically
                            if (m.estado_ventilador === 'ACTIVADO') {
                                // Si quisieras actualizar el texto del ventilador vía AJAX, aquí iría la lógica
                                // Por ahora, el location.reload() cada 30s se encarga de la vista completa
                             console.log("Ventilador: ACTIVADO");
                            } else {
                                console.log("Ventilador: DESACTIVADO");
                            }
                            $('#statusIndicator').removeClass('badge-danger').addClass('badge-success');
                            $('#statusText').text('ONLINE');
                        } else {
                            // If last report is more than 1 min old, status becomes OFFLINE
                            $('#statusIndicator').removeClass('badge-success').addClass('badge-danger');
                            $('#statusText').text('OFFLINE');
                        }
                    }
                }
            });
        }

        // Auto refresh setup
        function startAutoRefresh() {
            refreshInterval = setInterval(function() {
                // Fetch metrics via API
                fetchMetrics();
                
                // Reload page every 30 seconds to sync all data
                location.reload();
            }, 10000); // every 10 seconds
        }

        function stopAutoRefresh() {
            clearInterval(refreshInterval);
        }

        if ($('#autoRefreshToggle').is(':checked')) {
            startAutoRefresh();
        }

        $('#autoRefreshToggle').change(function() {
            if (this.checked) {
                $('#refreshIcon').addClass('fa-spin');
                startAutoRefresh();
            } else {
                $('#refreshIcon').removeClass('fa-spin');
                stopAutoRefresh();
            }
        });
    });
</script>

<style>
    @keyframes spin-fan {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    .fan-spinning {
        animation: spin-fan 1s linear infinite;
        display: inline-block;
    }
</style>


@endsection
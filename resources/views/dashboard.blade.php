@extends('layouts.app')

@section('title', 'Dashboard Principal')
@section('page_title', 'Monitoreo en Tiempo Real')

@section('content')
<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center flex-wrap">
        <p class="text-muted mb-2">Visualiza el estado actual del deshidratador solar y las últimas lecturas climatológicas.</p>
        <div class="custom-control custom-switch mb-2">
            <input type="checkbox" class="custom-control-input" id="autoRefreshToggle" checked>
            <label class="custom-control-label font-weight-bold text-dark" for="autoRefreshToggle">
                <i class="fas fa-sync fa-spin text-info mr-1" id="refreshIcon"></i> Actualización Automática (5s)
            </label>
        </div>
    </div>
</div>

<!-- Metric Cards -->
<div class="row">
    <!-- Temperatura Card -->
    <div class="col-lg-3 col-6">
        <div class="small-box metric-card bg-temp p-3">
            <div class="inner">
                <p class="mb-1 text-uppercase text-xs font-weight-bold opacity-8">Temperatura Actual</p>
                <h3 class="mb-0 font-weight-bold">
                    <span id="tempValue">{{ $ultimaLectura ? number_format($ultimaLectura->temperatura, 2) : 'N/A' }}</span> <small style="font-size: 20px; font-weight: 500;">°C</small>
                </h3>
            </div>
            <div class="icon" style="top: 15px; right: 15px;">
                <i class="fas fa-thermometer-half text-white opacity-4" style="font-size: 50px;"></i>
            </div>
        </div>
    </div>
    
    <!-- Humedad Card -->
    <div class="col-lg-3 col-6">
        <div class="small-box metric-card bg-hum p-3">
            <div class="inner">
                <p class="mb-1 text-uppercase text-xs font-weight-bold opacity-8">Humedad Relativa</p>
                <h3 class="mb-0 font-weight-bold">
                    <span id="humValue">{{ $ultimaLectura ? number_format($ultimaLectura->humedad, 2) : 'N/A' }}</span> <small style="font-size: 20px; font-weight: 500;">%</small>
                </h3>
            </div>
            <div class="icon" style="top: 15px; right: 15px;">
                <i class="fas fa-tint text-white opacity-4" style="font-size: 50px;"></i>
            </div>
        </div>
    </div>

    <!-- Presión Card -->
    <div class="col-lg-3 col-6">
        <div class="small-box metric-card bg-pres p-3">
            <div class="inner">
                <p class="mb-1 text-uppercase text-xs font-weight-bold opacity-8">Presión Atmosférica</p>
                <h3 class="mb-0 font-weight-bold">
                    <span id="presValue">{{ $ultimaLectura ? number_format($ultimaLectura->presion, 2) : 'N/A' }}</span> <small style="font-size: 16px; font-weight: 500;">hPa</small>
                </h3>
            </div>
            <div class="icon" style="top: 15px; right: 15px;">
                <i class="fas fa-tachometer-alt text-white opacity-4" style="font-size: 50px;"></i>
            </div>
        </div>
    </div>

    <!-- Última Actualización Card -->
    <div class="col-lg-3 col-6">
        <div class="small-box metric-card bg-update p-3">
            <div class="inner">
                <p class="mb-1 text-uppercase text-xs font-weight-bold opacity-8">Último Reporte</p>
                <h3 class="mb-0" style="font-size: 22px; font-weight: 700; line-height: 1.5; min-height: 38px;" id="updateValue">
                    {{ $ultimaLectura ? $ultimaLectura->fecha_hora->format('H:i:s') : 'N/A' }} 
                </h3>
                <span class="text-xs opacity-8 d-block" id="updateDate">
                    {{ $ultimaLectura ? $ultimaLectura->fecha_hora->format('d/m/Y') : 'Sin registros' }}
                </span>
            </div>
            <div class="icon" style="top: 15px; right: 15px;">
                <i class="far fa-clock text-white opacity-4" style="font-size: 50px;"></i>
            </div>
        </div>
    </div>
</div>

<!-- Table of Last 10 Readings -->
<div class="row mt-3">
    <div class="col-12">
        <div class="card shadow-sm border-0" style="border-radius: 16px;">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title text-lg font-weight-bold text-dark mb-0">
                    <i class="fas fa-list-ul text-warning mr-2"></i>Últimas 10 Lecturas Registradas
                </h3>
                <span class="badge badge-warning px-3 py-2 text-white font-weight-bold" style="border-radius: 30px;">Sensor BME280</span>
            </div>
            <!-- /.card-header -->
            <div class="card-body table-responsive p-0">
                <table class="table table-hover table-striped mb-0 text-center">
                    <thead class="bg-light">
                        <tr>
                            <th style="width: 10%">ID</th>
                            <th style="width: 22%">Temperatura (°C)</th>
                            <th style="width: 22%">Humedad (%)</th>
                            <th style="width: 22%">Presión (hPa)</th>
                            <th style="width: 24%">Fecha y Hora</th>
                        </tr>
                    </thead>
                    <tbody id="readingsTableBody">
                        @forelse($ultimasLecturas as $lectura)
                            <tr>
                                <td class="font-weight-bold text-muted">#{{ $lectura->id_lectura }}</td>
                                <td>
                                    <span class="badge badge-warning px-2 py-1 font-weight-bold text-dark" style="font-size: 14px;">
                                        {{ number_format($lectura->temperatura, 2) }} °C
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-info px-2 py-1 font-weight-bold" style="font-size: 14px;">
                                        {{ number_format($lectura->humedad, 2) }} %
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-success px-2 py-1 font-weight-bold" style="font-size: 14px;">
                                        {{ number_format($lectura->presion, 2) }} hPa
                                    </span>
                                </td>
                                <td class="text-secondary font-weight-bold">
                                    {{ $lectura->fecha_hora->format('d/m/Y H:i:s') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-5 text-muted">
                                    <i class="fas fa-info-circle fa-2x mb-3 text-gray"></i>
                                    <p class="mb-0">No se han registrado lecturas en el sistema todavía.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        let refreshInterval = null;

        // Auto refresh function
        function startAutoRefresh() {
            refreshInterval = setInterval(function() {
                // Fetch updated dashboard view via AJAX or just reload page content for simplicity and reliability
                // We'll perform a partial reload on the specific sections: metric cards and readings table
                location.reload();
            }, 5000);
        }

        function stopAutoRefresh() {
            clearInterval(refreshInterval);
        }

        // Initialize state
        if ($('#autoRefreshToggle').is(':checked')) {
            startAutoRefresh();
        }

        // Toggle handler
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
@endsection

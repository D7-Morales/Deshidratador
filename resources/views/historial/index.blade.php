@extends('layouts.app')

@section('title', 'Historial de Lecturas')
@section('page_title', 'Historial de Lecturas')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Filter Card -->
        <div class="card shadow-sm border-0 mb-4" style="border-radius: 16px;">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="card-title font-weight-bold text-dark mb-0">
                    <i class="fas fa-filter text-warning mr-2"></i>Filtros de Búsqueda
                </h5>
            </div>
            <div class="card-body px-4 pb-4">
                <form action="{{ route('historial.index') }}" method="GET">
                    <div class="row">
                        <!-- Date range -->
                        <div class="col-md-3 col-sm-6 mb-3">
                            <label for="fecha_desde" class="font-weight-bold text-muted text-xs text-uppercase">Fecha Desde</label>
                            <input type="date" name="fecha_desde" id="fecha_desde" class="form-control" value="{{ request('fecha_desde') }}">
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <label for="fecha_hasta" class="font-weight-bold text-muted text-xs text-uppercase">Fecha Hasta</label>
                            <input type="date" name="fecha_hasta" id="fecha_hasta" class="form-control" value="{{ request('fecha_hasta') }}">
                        </div>
                        <!-- Search word -->
                        <div class="col-md-3 col-sm-6 mb-3">
                            <label for="buscar" class="font-weight-bold text-muted text-xs text-uppercase">Buscar Valor</label>
                            <input type="text" name="buscar" id="buscar" class="form-control" placeholder="Ej. 25.30" value="{{ request('buscar') }}">
                        </div>
                        <!-- Sorting -->
                        <div class="col-md-3 col-sm-6 mb-3">
                            <label for="orden" class="font-weight-bold text-muted text-xs text-uppercase">Orden Cronológico</label>
                            <select name="orden" id="orden" class="form-control">
                                <option value="desc" {{ request('orden', 'desc') == 'desc' ? 'selected' : '' }}>Más reciente primero</option>
                                <option value="asc" {{ request('orden') == 'asc' ? 'selected' : '' }}>Más antiguo primero</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12 d-flex justify-content-end">
                            <a href="{{ route('historial.index') }}" class="btn btn-light border mr-2 px-4">
                                <i class="fas fa-undo-alt mr-1"></i> Limpiar
                            </a>
                            <button type="submit" class="btn btn-warning px-4 text-white font-weight-bold">
                                <i class="fas fa-search mr-1"></i> Filtrar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- /.card -->

        <!-- Results Table Card -->
        <div class="card shadow-sm border-0" style="border-radius: 16px;">
            <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center flex-wrap pt-4 px-4">
                <h3 class="card-title text-lg font-weight-bold text-dark mb-0">
                    <i class="fas fa-table text-warning mr-2"></i>Registros Climáticos Encontrados
                </h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body table-responsive p-0">
                <table class="table table-hover table-striped mb-0 text-center">
                    <thead class="bg-light text-muted">
                        <tr>
                            <th>ID</th>
                            <th>Sensor</th>
                            <th>Ubicación</th>
                            <th>Temperatura</th>
                            <th>Humedad</th>
                            <th>Presión</th>
                            <th>Fecha y Hora</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lecturas as $lectura)
                            <tr>
                                <td class="font-weight-bold text-muted">#{{ $lectura->id_lectura }}</td>
                                <td class="font-weight-bold text-dark">{{ $lectura->sensor->nombre_sensor ?? 'Desconocido' }}</td>
                                <td><span class="badge badge-light px-2 py-1">{{ $lectura->sensor->ubicacion ?? 'N/A' }}</span></td>
                                <td>
                                    <span class="badge badge-warning px-2 py-1 font-weight-bold text-dark" style="font-size: 13px;">
                                        {{ number_format($lectura->temperatura, 2) }} °C
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-info px-2 py-1 font-weight-bold" style="font-size: 13px;">
                                        {{ number_format($lectura->humedad, 2) }} %
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-success px-2 py-1 font-weight-bold" style="font-size: 13px;">
                                        {{ number_format($lectura->presion, 2) }} hPa
                                    </span>
                                </td>
                                <td class="text-secondary font-weight-bold">
                                    {{ $lectura->fecha_hora->format('d/m/Y H:i:s') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-5 text-muted">
                                    <i class="fas fa-info-circle fa-2x mb-3 text-gray"></i>
                                    <p class="mb-0">No se encontraron lecturas que coincidan con los filtros de búsqueda.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- /.card-body -->
            <div class="card-footer bg-white border-0 py-3 px-4 d-flex justify-content-center">
                {{ $lecturas->links() }}
            </div>
        </div>
        <!-- /.card -->
    </div>
</div>
@endsection

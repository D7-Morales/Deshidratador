@extends('layouts.app')

@section('title', 'Catálogo de Frutas')
@section('page_title', 'Administración de Frutas')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0" style="border-radius: 16px;">
            <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center flex-wrap pt-4 px-4">
                <h3 class="card-title text-lg font-weight-bold text-dark mb-0">
                    <i class="fas fa-apple-alt text-warning mr-2"></i>Catálogo de Frutas y Parámetros Recomendados
                </h3>
                <a href="{{ route('frutas.create') }}" class="btn btn-warning text-white font-weight-bold px-3 py-2" style="border-radius: 30px;">
                    <i class="fas fa-plus mr-1"></i> Registrar Nueva Fruta
                </a>
            </div>
            <!-- /.card-header -->
            <div class="card-body table-responsive p-0">
                <table class="table table-hover table-striped mb-0 text-center">
                    <thead class="bg-light text-muted">
                        <tr>
                            <th style="width: 8%">ID</th>
                            <th style="width: 15%">Fruta</th>
                            <th style="width: 15%">Temp. Rec. (°C)</th>
                            <th style="width: 15%">Hum. Rec. (%)</th>
                            <th style="width: 15%">Hum. Final (%)</th>
                            <th style="width: 10%">Tiempo (Hrs)</th>
                            <th style="width: 15%">Observaciones</th>
                            <th style="width: 12%">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($frutas as $fruta)
                            <tr>
                                <td class="font-weight-bold text-muted">#{{ $fruta->id_fruta }}</td>
                                <td class="font-weight-bold text-dark">{{ $fruta->nombre_fruta }}</td>
                                <td>
                                    <span class="badge bg-orange px-2 py-1 text-white font-weight-bold" style="font-size: 13px;">
                                        {{ number_format($fruta->temperatura_recomendada, 2) }} °C
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-info px-2 py-1 font-weight-bold" style="font-size: 13px;">
                                        {{ number_format($fruta->humedad_recomendada, 2) }} %
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-success px-2 py-1 font-weight-bold" style="font-size: 13px;">
                                        {{ $fruta->porcentaje_humedad_final ? number_format($fruta->porcentaje_humedad_final, 2) . ' %' : 'N/A' }}
                                    </span>
                                </td>
                                <td class="font-weight-bold text-secondary">
                                    {{ $fruta->tiempo_estimado_horas ?? 'N/A' }} hs
                                </td>
                                <td class="text-left text-muted">{{ Str::limit($fruta->observaciones, 70) }}</td>
                                <td class="py-2">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('frutas.edit', $fruta->id_fruta) }}" class="btn btn-info mr-1" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('frutas.destroy', $fruta->id_fruta) }}" method="POST" onsubmit="return confirm('¿Está seguro de eliminar esta fruta?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" title="Eliminar">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-5 text-muted">
                                    <i class="fas fa-info-circle fa-2x mb-3 text-gray"></i>
                                    <p class="mb-0">No se han registrado frutas en el sistema aún.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- /.card-body -->
            <div class="card-footer bg-white border-0 py-3 px-4 d-flex justify-content-center">
                {{ $frutas->links() }}
            </div>
        </div>
        <!-- /.card -->
    </div>
</div>
@endsection

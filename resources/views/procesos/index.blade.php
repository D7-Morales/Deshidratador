@extends('layouts.app')

@section('title', 'Procesos de Deshidratación')
@section('page_title', 'Procesos de Deshidratación')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0" style="border-radius: 16px;">
            <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center flex-wrap pt-4 px-4">
                <h3 class="card-title text-lg font-weight-bold text-dark mb-0">
                    <i class="fas fa-spinner text-warning mr-2"></i>Monitoreo de Procesos (Cargas de Fruta)
                </h3>
                <a href="{{ route('procesos.create') }}" class="btn btn-warning text-white font-weight-bold px-3 py-2" style="border-radius: 30px;">
                    <i class="fas fa-play mr-1"></i> Iniciar Nuevo Proceso
                </a>
            </div>
            <!-- /.card-header -->
            <div class="card-body table-responsive p-0">
                <table class="table table-hover table-striped mb-0 text-center">
                    <thead class="bg-light text-muted">
                        <tr>
                            <th>ID</th>
                            <th>Lote</th>
                            <th>Fruta</th>
                            <th>Deshidratador</th>
                            <th>Bandeja</th>
                            <th>Estado</th>
                            <th>Peso Inicial</th>
                            <th>Peso Final</th>
                            <th>Fecha Inicio</th>
                            <th>Rendimiento</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($procesos as $proceso)
                            <tr>
                                <td class="font-weight-bold text-muted">#{{ $proceso->id_carga }}</td>
                                <td class="font-weight-bold"><code>{{ $proceso->numero_lote ?? 'N/A' }}</code></td>
                                <td class="font-weight-bold text-dark">{{ $proceso->fruta->nombre_fruta ?? 'N/A' }}</td>
                                <td>{{ $proceso->deshidratador->nombre ?? 'N/A' }}</td>
                                <td><span class="badge badge-light px-2 py-1">Bandeja {{ $proceso->bandeja }}</span></td>
                                <td>
                                    @if(strtolower($proceso->estado_proceso) === 'activo')
                                        <span class="badge badge-warning px-3 py-2 text-dark font-weight-bold" style="border-radius: 20px;">
                                            <i class="fas fa-sync fa-spin mr-1"></i> ACTIVO
                                        </span>
                                    @elseif(strtolower($proceso->estado_proceso) === 'completado')
                                        <span class="badge badge-success px-3 py-2 font-weight-bold text-white" style="border-radius: 20px;">
                                            <i class="fas fa-check-circle mr-1"></i> COMPLETADO
                                        </span>
                                    @elseif(strtolower($proceso->estado_proceso) === 'pendiente')
                                        <span class="badge badge-info px-3 py-2 font-weight-bold" style="border-radius: 20px;">
                                            <i class="far fa-clock mr-1"></i> PENDIENTE
                                        </span>
                                    @else
                                        <span class="badge badge-danger px-3 py-2 font-weight-bold" style="border-radius: 20px;">
                                            <i class="fas fa-ban mr-1"></i> CANCELADO
                                        </span>
                                    @endif
                                </td>
                                <td class="font-weight-bold text-secondary">
                                    {{ number_format($proceso->peso_inicial_gramos / 1000, 2) }} kg
                                </td>
                                <td class="font-weight-bold text-secondary">
                                    {{ $proceso->peso_final_gramos ? number_format($proceso->peso_final_gramos / 1000, 2) . ' kg' : '-' }}
                                </td>
                                <td>{{ $proceso->fecha_inicio->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if($proceso->peso_final_gramos)
                                        @php
                                            $porcentajePerdida = (($proceso->peso_inicial_gramos - $proceso->peso_final_gramos) / $proceso->peso_inicial_gramos) * 100;
                                            $rendimiento = 100 - $porcentajePerdida;
                                        @endphp
                                        <span class="font-weight-bold text-success" title="Rendimiento del peso final frente al inicial">
                                            {{ number_format($rendimiento, 1) }}%
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="py-2">
                                    <div class="btn-group btn-group-sm">
                                        @if(strtolower($proceso->estado_proceso) === 'activo')
                                            <a href="{{ route('procesos.edit', $proceso->id_carga) }}" class="btn btn-success font-weight-bold mr-1" title="Finalizar Proceso">
                                                <i class="fas fa-check mr-1"></i> Finalizar
                                            </a>
                                        @endif
                                        <form action="{{ route('procesos.destroy', $proceso->id_carga) }}" method="POST" onsubmit="return confirm('¿Está seguro de eliminar el registro de este proceso?');">
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
                                <td colspan="11" class="py-5 text-muted">
                                    <i class="fas fa-info-circle fa-2x mb-3 text-gray"></i>
                                    <p class="mb-0">No se han registrado procesos de deshidratación.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- /.card-body -->
            <div class="card-footer bg-white border-0 py-3 px-4 d-flex justify-content-center">
                {{ $procesos->links() }}
            </div>
        </div>
        <!-- /.card -->
    </div>
</div>
@endsection

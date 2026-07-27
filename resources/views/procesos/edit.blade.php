@extends('layouts.app')

@section('title', 'Finalizar Proceso')
@section('page_title', 'Finalizar Proceso de Deshidratación')

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card shadow-sm border-0" style="border-radius: 16px;">
            <div class="card-header bg-transparent border-bottom pt-4 px-4">
                <h3 class="card-title font-weight-bold text-dark mb-0">
                    <i class="fas fa-check-circle text-success mr-2"></i>Completar Ciclo: #{{ $proceso->id_carga }}
                </h3>
            </div>
            <!-- /.card-header -->
            <form action="{{ route('procesos.update', $proceso->id_carga) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body px-4 py-4">
                    
                    <!-- Reference info -->
                    <div class="row mb-4 p-3 bg-light rounded" style="border-left: 4px solid #f59e0b;">
                        <div class="col-md-3">
                            <span class="d-block text-xs text-muted text-uppercase font-weight-bold">Fruta</span>
                            <span class="font-weight-bold text-dark text-md">{{ $proceso->fruta->nombre_fruta ?? 'N/A' }}</span>
                        </div>
                        <div class="col-md-3">
                            <span class="d-block text-xs text-muted text-uppercase font-weight-bold">Deshidratador</span>
                            <span class="font-weight-bold text-dark text-md">{{ $proceso->deshidratador->nombre ?? 'N/A' }}</span>
                        </div>
                        <div class="col-md-3">
                            <span class="d-block text-xs text-muted text-uppercase font-weight-bold">Peso Inicial</span>
                            <span class="font-weight-bold text-dark text-md">{{ number_format($proceso->peso_inicial_gramos / 1000, 2) }} kg</span>
                        </div>
                        <div class="col-md-3">
                            <span class="d-block text-xs text-muted text-uppercase font-weight-bold">Iniciado el</span>
                            <span class="font-weight-bold text-dark text-md">{{ $proceso->fecha_inicio->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Peso Final -->
                        <div class="col-md-6 form-group mb-3">
                            <label for="peso_final" class="font-weight-bold text-muted text-xs text-uppercase">Peso Final Registrado (Kilogramos)</label>
                            <div class="input-group">
                                <input type="number" step="0.01" name="peso_final" id="peso_final" class="form-control @error('peso_final') is-invalid @enderror" placeholder="Ej. 0.35" value="{{ old('peso_final') }}" max="{{ $proceso->peso_inicial_gramos / 1000 }}" required>
                                <div class="input-group-append">
                                    <span class="input-group-text font-weight-bold bg-light">kg</span>
                                </div>
                                @error('peso_final')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="text-muted">Debe ser menor o igual a {{ number_format($proceso->peso_inicial_gramos / 1000, 2) }} kg.</small>
                        </div>

                        <!-- Fecha Fin -->
                        <div class="col-md-6 form-group mb-3">
                            <label for="fecha_fin" class="font-weight-bold text-muted text-xs text-uppercase">Fecha y Hora de Finalización</label>
                            <input type="datetime-local" name="fecha_fin" id="fecha_fin" class="form-control @error('fecha_fin') is-invalid @enderror" value="{{ old('fecha_fin', now()->format('Y-m-d\TH:i')) }}" required>
                            @error('fecha_fin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Observaciones -->
                    <div class="form-group mb-3">
                        <label for="observaciones" class="font-weight-bold text-muted text-xs text-uppercase">Observaciones de Cierre</label>
                        <textarea name="observaciones" id="observaciones" class="form-control @error('observaciones') is-invalid @enderror" rows="4" placeholder="Ej. Deshidratación completada correctamente. Reducción de volumen del 80%. Frutas secas con buena consistencia y aroma...">{{ old('observaciones', $proceso->observaciones) }}</textarea>
                        @error('observaciones')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
                <!-- /.card-body -->
                <div class="card-footer bg-transparent border-top py-3 px-4 d-flex justify-content-between">
                    <a href="{{ route('procesos.index') }}" class="btn btn-light border px-4">
                        <i class="fas fa-arrow-left mr-1"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-success px-4 font-weight-bold text-white">
                        <i class="fas fa-check-circle mr-1"></i> Finalizar Proceso
                    </button>
                </div>
            </form>
        </div>
        <!-- /.card -->
    </div>
</div>
@endsection

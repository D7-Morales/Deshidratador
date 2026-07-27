@extends('layouts.app')

@section('title', 'Editar Fruta')
@section('page_title', 'Editar Fruta')

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card shadow-sm border-0" style="border-radius: 16px;">
            <div class="card-header bg-transparent border-bottom pt-4 px-4">
                <h3 class="card-title font-weight-bold text-dark mb-0">
                    <i class="fas fa-edit text-warning mr-2"></i>Modificar Información: {{ $fruta->nombre_fruta }}
                </h3>
            </div>
            <!-- /.card-header -->
            <form action="{{ route('frutas.update', $fruta->id_fruta) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body px-4 py-4">
                    
                    <div class="form-group mb-3">
                        <label for="nombre_fruta" class="font-weight-bold text-muted text-xs text-uppercase">Nombre de la Fruta</label>
                        <input type="text" name="nombre_fruta" id="nombre_fruta" class="form-control @error('nombre_fruta') is-invalid @enderror" value="{{ old('nombre_fruta', $fruta->nombre_fruta) }}" required>
                        @error('nombre_fruta')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-4 form-group mb-3">
                            <label for="temperatura_recomendada" class="font-weight-bold text-muted text-xs text-uppercase">Temperatura Óptima (°C)</label>
                            <input type="number" step="0.01" name="temperatura_recomendada" id="temperatura_recomendada" class="form-control @error('temperatura_recomendada') is-invalid @enderror" value="{{ old('temperatura_recomendada', $fruta->temperatura_recomendada) }}" required>
                            @error('temperatura_recomendada')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 form-group mb-3">
                            <label for="humedad_recomendada" class="font-weight-bold text-muted text-xs text-uppercase">Humedad Recomendada (%)</label>
                            <input type="number" step="0.01" name="humedad_recomendada" id="humedad_recomendada" class="form-control @error('humedad_recomendada') is-invalid @enderror" value="{{ old('humedad_recomendada', $fruta->humedad_recomendada) }}" required>
                            @error('humedad_recomendada')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 form-group mb-3">
                            <label for="porcentaje_humedad_final" class="font-weight-bold text-muted text-xs text-uppercase">Humedad Final (%)</label>
                            <input type="number" step="0.01" name="porcentaje_humedad_final" id="porcentaje_humedad_final" class="form-control @error('porcentaje_humedad_final') is-invalid @enderror" value="{{ old('porcentaje_humedad_final', $fruta->porcentaje_humedad_final) }}">
                            @error('porcentaje_humedad_final')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="tiempo_estimado_horas" class="font-weight-bold text-muted text-xs text-uppercase">Tiempo Estimado (Horas)</label>
                        <input type="number" name="tiempo_estimado_horas" id="tiempo_estimado_horas" class="form-control @error('tiempo_estimado_horas') is-invalid @enderror" value="{{ old('tiempo_estimado_horas', $fruta->tiempo_estimado_horas) }}">
                        @error('tiempo_estimado_horas')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="observaciones" class="font-weight-bold text-muted text-xs text-uppercase">Observaciones (Opcional)</label>
                        <textarea name="observaciones" id="observaciones" class="form-control @error('observaciones') is-invalid @enderror" rows="3" maxlength="300">{{ old('observaciones', $fruta->observaciones) }}</textarea>
                        @error('observaciones')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
                <!-- /.card-body -->
                <div class="card-footer bg-transparent border-top py-3 px-4 d-flex justify-content-between">
                    <a href="{{ route('frutas.index') }}" class="btn btn-light border px-4">
                        <i class="fas fa-arrow-left mr-1"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-warning px-4 text-white font-weight-bold">
                        <i class="fas fa-save mr-1"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
        <!-- /.card -->
    </div>
</div>
@endsection

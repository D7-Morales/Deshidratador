@extends('layouts.app')

@section('title', 'Iniciar Proceso')
@section('page_title', 'Iniciar Nuevo Proceso de Deshidratación')

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card shadow-sm border-0" style="border-radius: 16px;">
            <div class="card-header bg-transparent border-bottom pt-4 px-4">
                <h3 class="card-title font-weight-bold text-dark mb-0">
                    <i class="fas fa-play-circle text-warning mr-2"></i>Iniciar Ciclo de Deshidratación
                </h3>
            </div>
            <!-- /.card-header -->
            <form action="{{ route('procesos.store') }}" method="POST">
                @csrf
                <div class="card-body px-4 py-4">
                    
                    <div class="row">
                        <!-- Fruit select -->
                        <div class="col-md-6 form-group mb-4">
                            <label for="id_fruta" class="font-weight-bold text-muted text-xs text-uppercase">Seleccione la Fruta</label>
                            <select name="id_fruta" id="id_fruta" class="form-control @error('id_fruta') is-invalid @enderror" required>
                                <option value="">-- Elija una fruta --</option>
                                @foreach($frutas as $fruta)
                                    <option value="{{ $fruta->id_fruta }}" {{ old('id_fruta') == $fruta->id_fruta ? 'selected' : '' }}>
                                        {{ $fruta->nombre_fruta }} (Recomendado: {{ number_format($fruta->temperatura_recomendada, 1) }}°C / {{ number_format($fruta->humedad_recomendada, 1) }}%)
                                    </option>
                                @endforeach
                            </select>
                            @error('id_fruta')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Dehydrator select -->
                        <div class="col-md-6 form-group mb-4">
                            <label for="id_deshidratador" class="font-weight-bold text-muted text-xs text-uppercase">Deshidratador Solar</label>
                            <select name="id_deshidratador" id="id_deshidratador" class="form-control @error('id_deshidratador') is-invalid @enderror" required>
                                <option value="">-- Seleccione el deshidratador --</option>
                                @foreach($deshidratadores as $desh)
                                    <option value="{{ $desh->id_deshidratador }}" {{ old('id_deshidratador') == $desh->id_deshidratador ? 'selected' : '' }}>
                                        {{ $desh->nombre }} ({{ $desh->ubicacion ?? 'Sin ubicación' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('id_deshidratador')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <!-- Bandeja -->
                        <div class="col-md-4 form-group mb-3">
                            <label for="bandeja" class="font-weight-bold text-muted text-xs text-uppercase">Número de Bandeja</label>
                            <input type="number" min="1" name="bandeja" id="bandeja" class="form-control @error('bandeja') is-invalid @enderror" placeholder="Ej. 1" value="{{ old('bandeja', 1) }}" required>
                            @error('bandeja')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Cantidad Frutas -->
                        <div class="col-md-4 form-group mb-3">
                            <label for="cantidad_frutas" class="font-weight-bold text-muted text-xs text-uppercase">Cantidad de Frutas (Unidades)</label>
                            <input type="number" min="1" name="cantidad_frutas" id="cantidad_frutas" class="form-control @error('cantidad_frutas') is-invalid @enderror" placeholder="Ej. 25" value="{{ old('cantidad_frutas', 1) }}" required>
                            @error('cantidad_frutas')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Peso Inicial (kg) -->
                        <div class="col-md-4 form-group mb-3">
                            <label for="peso_inicial" class="font-weight-bold text-muted text-xs text-uppercase">Peso Inicial (Kilogramos)</label>
                            <div class="input-group">
                                <input type="number" step="0.01" name="peso_inicial" id="peso_inicial" class="form-control @error('peso_inicial') is-invalid @enderror" placeholder="Ej. 1.50" value="{{ old('peso_inicial') }}" required>
                                <div class="input-group-append">
                                    <span class="input-group-text font-weight-bold bg-light">kg</span>
                                </div>
                                @error('peso_inicial')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="fecha_inicio" class="font-weight-bold text-muted text-xs text-uppercase">Fecha y Hora de Inicio</label>
                        <input type="datetime-local" name="fecha_inicio" id="fecha_inicio" class="form-control @error('fecha_inicio') is-invalid @enderror" value="{{ old('fecha_inicio', now()->format('Y-m-d\TH:i')) }}" required>
                        @error('fecha_inicio')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="observaciones" class="font-weight-bold text-muted text-xs text-uppercase">Observaciones Iniciales</label>
                        <textarea name="observaciones" id="observaciones" class="form-control @error('observaciones') is-invalid @enderror" rows="3" placeholder="Ej. Rodajas de manzana en bandeja 1, deshidratador solar principal...">{{ old('observaciones') }}</textarea>
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
                    <button type="submit" class="btn btn-warning px-4 text-white font-weight-bold">
                        <i class="fas fa-play mr-1"></i> Iniciar Proceso
                    </button>
                </div>
            </form>
        </div>
        <!-- /.card -->
    </div>
</div>
@endsection

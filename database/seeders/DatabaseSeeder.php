<?php

namespace Database\Seeders;

use App\Models\Usuario;
use App\Models\Sensor;
use App\Models\Fruta;
use App\Models\Rol;
use App\Models\Deshidratador;
use App\Models\TipoSensor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Roles
        $adminRol = Rol::firstOrCreate(
            ['id_rol' => 1],
            ['nombre_rol' => 'admin', 'descripcion' => 'Administrador del sistema con control total']
        );
        $comercianteRol = Rol::firstOrCreate(
            ['id_rol' => 2],
            ['nombre_rol' => 'comerciante', 'descripcion' => 'Usuario que gestiona procesos de deshidratación']
        );

        // 2. Seed Deshidratadores
        $desh1 = Deshidratador::firstOrCreate(
            ['id_deshidratador' => 1],
            [
                'nombre' => 'Deshidratador Principal',
                'ubicacion' => 'Mercado La Pampa - Cochabamba',
                'capacidad_kg' => 3.00,
                'panel_solar' => 1,
                'bateria' => 1,
                'estado' => 'activo'
            ]
        );
        $desh2 = Deshidratador::firstOrCreate(
            ['id_deshidratador' => 2],
            [
                'nombre' => 'Deshidratador Solar Principal',
                'ubicacion' => 'Cámara de Deshidratación Principal - Mercado La Pampa',
                'capacidad_kg' => 3.00,
                'panel_solar' => 1,
                'bateria' => 1,
                'estado' => 'activo'
            ]
        );

        // 3. Seed Tipo de Sensor
        $tipoBme = TipoSensor::firstOrCreate(
            ['id_tipo' => 4],
            [
                'nombre_tipo' => 'BME280',
                'fabricante' => 'Bosch Sensortec',
                'interfaz' => 'I2C',
                'precision_temperatura' => '±1°C',
                'precision_humedad' => '±3% HR',
                'voltaje_operacion' => 3.30,
                'descripcion' => 'Sensor de temperatura, humedad y presión'
            ]
        );

        // 4. Seed User (Diego, password: 12345 using Bcrypt)
        if (!Usuario::where('email_usuario', 'diego@deshidratador.com')->exists()) {
            Usuario::create([
                'ci_usuario' => '12345678',
                'nombres_usuario' => 'Diego Bladimir',
                'apellidos_usuario' => 'Morales Pantigozo',
                'email_usuario' => 'diego@deshidratador.com',
                'password_hash' => Hash::make('12345'),
                'id_rol' => $adminRol->id_rol,
                'estado_usuario' => 'activo',
            ]);
        }

        // 5. Seed Sensor
        if (!Sensor::where('nombre_sensor', 'Sensor BME280 Cámara 1')->exists()) {
            Sensor::create([
                'id_deshidratador' => $desh1->id_deshidratador,
                'id_tipo' => $tipoBme->id_tipo,
                'nombre_sensor' => 'Sensor BME280 Cámara 1',
                'modelo' => 'BME280 I2C',
                'ubicacion_sensor' => 'Cámara de Deshidratación Principal',
                'estado_sensor' => 'activo',
            ]);
        }

        // 6. Seed Fruits matching new columns
        $fruits = [
            [
                'nombre_fruta' => 'Manzana',
                'temperatura_recomendada' => 57.50,
                'humedad_recomendada' => 20.00,
                'porcentaje_humedad_final' => 10.00,
                'tiempo_estimado_horas' => 12,
                'observaciones' => 'Manzana cortada en rodajas de 5mm para deshidratado crujiente.'
            ],
            [
                'nombre_fruta' => 'Plátano',
                'temperatura_recomendada' => 62.00,
                'humedad_recomendada' => 15.00,
                'porcentaje_humedad_final' => 12.00,
                'tiempo_estimado_horas' => 18,
                'observaciones' => 'Plátano en rodajas de 6mm, requiere mayor tiempo por su alto contenido de azúcar.'
            ],
            [
                'nombre_fruta' => 'Frutilla',
                'temperatura_recomendada' => 55.00,
                'humedad_recomendada' => 22.00,
                'porcentaje_humedad_final' => 15.00,
                'tiempo_estimado_horas' => 10,
                'observaciones' => 'Frutillas cortadas a la mitad. Deshidratado suave y gomoso.'
            ],
            [
                'nombre_fruta' => 'Naranja',
                'temperatura_recomendada' => 60.00,
                'humedad_recomendada' => 18.00,
                'porcentaje_humedad_final' => 8.00,
                'tiempo_estimado_horas' => 14,
                'observaciones' => 'Naranja en rodajas finas con cáscara. Excelente para decoración y repostería.'
            ]
        ];

        foreach ($fruits as $fruit) {
            Fruta::firstOrCreate(
                ['nombre_fruta' => $fruit['nombre_fruta']],
                [
                    'temperatura_recomendada' => $fruit['temperatura_recomendada'],
                    'humedad_recomendada' => $fruit['humedad_recomendada'],
                    'porcentaje_humedad_final' => $fruit['porcentaje_humedad_final'],
                    'tiempo_estimado_horas' => $fruit['tiempo_estimado_horas'],
                    'observaciones' => $fruit['observaciones']
                ]
            );
        }
    }
}

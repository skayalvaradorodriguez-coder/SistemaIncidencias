<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TipoIncidencia;

class TiposIncidenciaSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            'Infraestructura' => [
                'Alumbrado público',
                'Bache en vía',
                'Semáforo dañado',
                'Alcantarillado',
                'Vía en mal estado',
            ],
            'Seguridad' => [
                'Robo',
                'Vandalismo',
                'Riña / Altercado',
                'Punto de venta de drogas',
            ],
            'Ambiental' => [
                'Acumulación de basura',
                'Contaminación de agua',
                'Tala ilegal',
                'Quema no autorizada',
            ],
            'Servicios Básicos' => [
                'Corte de agua potable',
                'Corte de energía eléctrica',
                'Fuga de agua',
            ],
            'Salud Pública' => [
                'Foco de plagas',
                'Animal en la vía pública',
            ],
        ];

        foreach ($tipos as $nombreTipo => $subtipos) {
            $tipo = TipoIncidencia::firstOrCreate(
                ['nombre' => $nombreTipo],
                ['descripcion' => "Incidencias relacionadas con {$nombreTipo}"]
            );

            foreach ($subtipos as $nombreSubtipo) {
                $tipo->subtipos()->firstOrCreate(
                    ['nombre' => $nombreSubtipo],
                    ['descripcion' => null]
                );
            }
        }
    }
}
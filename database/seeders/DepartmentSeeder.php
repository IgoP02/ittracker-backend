<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $deps = [
            "Dirección" => 3,
            "Producción" => 2,
            "Recursos Humanos" => 3,
            "Finanzas" => 1,
            "Bienes Públicos" => 1,
            "Servicio Médico" => 1,
            "IT" => 3,
            "Logística Terrestre" => 3,
            "SSSH" => 1,
            "Materia Prima" => 2,
            "Control de Calidad" => 1,
            "C. de Formación" => 1,
            "Compras" => 3,
            "Almacén" => 3,
            "Contabilidad" => 2,
            "Automatización" => 3,
            "Mantenimiento" => 2,
            "Envase y Despacho" => 2,
            "Protección Física" => 2,
            "Premezclado" => 1,
        ];

        foreach ($deps as $name => $value) {
            Department::create([
                "name" => $name,
                "priority" => $value,
            ]);
        }
    }
}

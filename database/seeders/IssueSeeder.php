<?php

namespace Database\Seeders;

use App\Models\Issue;
use Illuminate\Database\Seeder;

class IssueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $issues = [
            [
                "Sin conexión a internet",
                "Conexión a internet lenta",
                "No puedo acceder a un sitio",
                "Otro",
            ],
            [
                "Problemas con SAP ERP",
                "Problemas con IBM Lotus",
                "Problemas para conexión remota",
                "Otro",
            ],
            [
                "Problemas con paquete Office",
                "Problemas con software de edición",
                "Problemas con software específico de departamento",
                "Otro",
            ],
            [
                "Equipo ha sufrido daño físico",
                "Equipo emite sonidos inusuales",
                "Equipo no enciende",
                "Mal rendimiento",
                "Problemas con impresoras, scanners, etc.",
            ],
            [
                "Problemas para usar un programa",
                "Pérdida de información",
                "Otro",
            ],
        ];

        foreach ($issues as $key => $array) {
            foreach ($array as $key2 => $value) {
                Issue::create([
                    "name" => $value,
                    "issue_type_id" => $key + 1,

                ]);
            }
        }
    }
}

<?php

namespace Modules\FPSplanificationstage\Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::firstOrCreate(["name" => "fpsplanificationstage::gerer_le_module", "guard_name" => "web"]);
    }
}

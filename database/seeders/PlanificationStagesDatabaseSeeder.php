<?php

namespace Modules\PlanificationStages\Database\Seeders;

use Illuminate\Database\Seeder;

class PlanificationStagesDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            PermissionsSeeder::class
        ]);
    }
}

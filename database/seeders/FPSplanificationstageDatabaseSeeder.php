<?php

namespace Modules\FPSplanificationstage\Database\Seeders;

use Illuminate\Database\Seeder;

class FPSplanificationstageDatabaseSeeder extends Seeder
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

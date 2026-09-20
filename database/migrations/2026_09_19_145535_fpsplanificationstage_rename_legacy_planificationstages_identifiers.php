<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private string $legacyPrefix =
        'planificationstages_';

    private string $newPrefix =
        'fpsplanificationstage_';

    private string $legacyPermission =
        'planificationstages::gerer_le_module';

    private string $newPermission =
        'fpsplanificationstage::gerer_le_module';

    public function up(): void
    {
        $tables =
            Schema::getTableListing();

        foreach ($tables as $rawTable) {
            $table =
                str_contains(
                    $rawTable,
                    '.'
                )
                    ? substr(
                        $rawTable,
                        strrpos(
                            $rawTable,
                            '.'
                        ) + 1
                    )
                    : $rawTable;

            if (! str_starts_with(
                $table,
                $this->legacyPrefix
            )) {
                continue;
            }

            $target =
                $this->newPrefix
                . substr(
                    $table,
                    strlen(
                        $this->legacyPrefix
                    )
                );

            if (Schema::hasTable($target)) {
                throw new RuntimeException(
                    "Table cible déjà présente : {$target}"
                );
            }

            Schema::rename(
                $table,
                $target
            );
        }

        if (! Schema::hasTable('permissions')) {
            return;
        }

        $legacyExists =
            DB::table('permissions')
                ->where(
                    'name',
                    $this->legacyPermission
                )
                ->exists();

        $newExists =
            DB::table('permissions')
                ->where(
                    'name',
                    $this->newPermission
                )
                ->exists();

        if ($legacyExists && $newExists) {
            throw new RuntimeException(
                'Les deux permissions ancienne et nouvelle existent.'
            );
        }

        if ($legacyExists) {
            DB::table('permissions')
                ->where(
                    'name',
                    $this->legacyPermission
                )
                ->update([
                    'name' =>
                        $this->newPermission,
                ]);
        }
    }

    public function down(): void
    {
        $tables =
            Schema::getTableListing();

        foreach ($tables as $rawTable) {
            $table =
                str_contains(
                    $rawTable,
                    '.'
                )
                    ? substr(
                        $rawTable,
                        strrpos(
                            $rawTable,
                            '.'
                        ) + 1
                    )
                    : $rawTable;

            if (! str_starts_with(
                $table,
                $this->newPrefix
            )) {
                continue;
            }

            $target =
                $this->legacyPrefix
                . substr(
                    $table,
                    strlen(
                        $this->newPrefix
                    )
                );

            if (Schema::hasTable($target)) {
                throw new RuntimeException(
                    "Table cible déjà présente : {$target}"
                );
            }

            Schema::rename(
                $table,
                $target
            );
        }

        if (! Schema::hasTable('permissions')) {
            return;
        }

        $legacyExists =
            DB::table('permissions')
                ->where(
                    'name',
                    $this->legacyPermission
                )
                ->exists();

        $newExists =
            DB::table('permissions')
                ->where(
                    'name',
                    $this->newPermission
                )
                ->exists();

        if ($newExists && ! $legacyExists) {
            DB::table('permissions')
                ->where(
                    'name',
                    $this->newPermission
                )
                ->update([
                    'name' =>
                        $this->legacyPermission,
                ]);
        }
    }
};

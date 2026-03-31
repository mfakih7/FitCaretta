<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private string $table = 'product_images';
    private string $column = 'color_id';
    private string $indexName = 'product_images_color_id_idx';
    private string $fkName = 'product_images_color_id_fk';

    public function up(): void
    {
        // Ensure InnoDB (required for foreign keys). Safe no-op if already InnoDB.
        $this->tryStatement("ALTER TABLE `{$this->table}` ENGINE=InnoDB");
        $this->tryStatement("ALTER TABLE `colors` ENGINE=InnoDB");

        // 1) Add column if missing
        if (! Schema::hasColumn($this->table, $this->column)) {
            Schema::table($this->table, function (Blueprint $table) {
                $table->unsignedBigInteger($this->column)->nullable()->after('product_id');
            });
        }

        // 2) Ensure correct type (unsignedBigInteger) + nullable (do not assume doctrine/dbal is installed)
        $this->tryStatement("ALTER TABLE `{$this->table}` MODIFY `{$this->column}` BIGINT UNSIGNED NULL");

        // 3) Drop any existing FK(s) that reference this column (prevents errno 121 duplicate key name)
        foreach ($this->existingForeignKeysForColumn($this->table, $this->column) as $constraintName) {
            $this->tryStatement("ALTER TABLE `{$this->table}` DROP FOREIGN KEY `{$constraintName}`");
        }

        // 4) Drop any existing index with our target name (or any other index name if it conflicts)
        if ($this->indexExists($this->table, $this->indexName)) {
            $this->tryStatement("ALTER TABLE `{$this->table}` DROP INDEX `{$this->indexName}`");
        }

        // 5) Create index with explicit name (only if missing)
        if (! $this->indexExists($this->table, $this->indexName)) {
            $this->tryStatement("ALTER TABLE `{$this->table}` ADD INDEX `{$this->indexName}` (`{$this->column}`)");
        }

        // 6) Create FK with explicit name (only if missing)
        if (! $this->foreignKeyExists($this->table, $this->fkName)) {
            $this->tryStatement(
                "ALTER TABLE `{$this->table}` " .
                "ADD CONSTRAINT `{$this->fkName}` " .
                "FOREIGN KEY (`{$this->column}`) REFERENCES `colors`(`id`) ON DELETE SET NULL"
            );
        }
    }

    public function down(): void
    {
        // Drop FK by explicit name if present, otherwise drop any FK for the column.
        if ($this->foreignKeyExists($this->table, $this->fkName)) {
            $this->tryStatement("ALTER TABLE `{$this->table}` DROP FOREIGN KEY `{$this->fkName}`");
        } else {
            foreach ($this->existingForeignKeysForColumn($this->table, $this->column) as $constraintName) {
                $this->tryStatement("ALTER TABLE `{$this->table}` DROP FOREIGN KEY `{$constraintName}`");
            }
        }

        // Drop index with explicit name if present
        if ($this->indexExists($this->table, $this->indexName)) {
            $this->tryStatement("ALTER TABLE `{$this->table}` DROP INDEX `{$this->indexName}`");
        }

        // Keep column for backwards compatibility unless you explicitly want it removed.
        // (Removing it could break existing code/data; leaving it is the safest production rollback behavior.)
        // If you do want it removed, uncomment below:
        // if (Schema::hasColumn($this->table, $this->column)) {
        //     Schema::table($this->table, function (Blueprint $table) {
        //         $table->dropColumn($this->column);
        //     });
        // }
    }

    private function tryStatement(string $sql): void
    {
        try {
            DB::statement($sql);
        } catch (\Throwable $e) {
            // Intentionally swallow to make migration idempotent/safe on partially-applied production schemas.
        }
    }

    private function indexExists(string $table, string $indexName): bool
    {
        try {
            $rows = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName]);
            return count($rows) > 0;
        } catch (\Throwable) {
            return false;
        }
    }

    private function foreignKeyExists(string $table, string $fkName): bool
    {
        try {
            $rows = DB::select(
                "SELECT CONSTRAINT_NAME
                 FROM information_schema.TABLE_CONSTRAINTS
                 WHERE CONSTRAINT_SCHEMA = DATABASE()
                   AND TABLE_NAME = ?
                   AND CONSTRAINT_NAME = ?
                   AND CONSTRAINT_TYPE = 'FOREIGN KEY'
                 LIMIT 1",
                [$table, $fkName]
            );
            return count($rows) > 0;
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * Returns all FK constraint names for a given column (any name).
     */
    private function existingForeignKeysForColumn(string $table, string $column): array
    {
        try {
            $rows = DB::select(
                "SELECT CONSTRAINT_NAME
                 FROM information_schema.KEY_COLUMN_USAGE
                 WHERE TABLE_SCHEMA = DATABASE()
                   AND TABLE_NAME = ?
                   AND COLUMN_NAME = ?
                   AND REFERENCED_TABLE_NAME IS NOT NULL",
                [$table, $column]
            );

            return collect($rows)
                ->pluck('CONSTRAINT_NAME')
                ->filter()
                ->unique()
                ->values()
                ->all();
        } catch (\Throwable) {
            return [];
        }
    }
};


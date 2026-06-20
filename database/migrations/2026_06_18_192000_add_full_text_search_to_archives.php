<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds a generated tsvector column for PostgreSQL full-text search
     * on the archives table. Weights: title (A), description (B).
     *
     * Note: This migration is PostgreSQL-specific and is skipped on
     * other database drivers (e.g., SQLite used in tests).
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        Schema::table('archives', function (Blueprint $table) {
            // Add tsvector column — generated column approach requires
            // PostgreSQL 12+. We use a stored generated column.
            DB::statement("
                ALTER TABLE archives
                ADD COLUMN search_vector tsvector
                GENERATED ALWAYS AS (
                    setweight(to_tsvector('english', coalesce(title, '')), 'A') ||
                    setweight(to_tsvector('english', coalesce(description, '')), 'B')
                ) STORED
            ");
        });

        // Create GIN index for efficient full-text search queries
        DB::statement('CREATE INDEX archives_search_vector_idx ON archives USING GIN (search_vector)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        Schema::table('archives', function (Blueprint $table) {
            DB::statement('DROP INDEX IF EXISTS archives_search_vector_idx');
            DB::statement('ALTER TABLE archives DROP COLUMN IF EXISTS search_vector');
        });
    }
};

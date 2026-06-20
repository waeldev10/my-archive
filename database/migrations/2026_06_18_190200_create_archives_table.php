<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates the base archives table with common fields shared across
     * all 16 archive types. Type-specific fields are stored in extension
     * tables (archive_links, archive_images, etc.).
     *
     * Content storage:
     * - Note, Article, Idea, Bookmark, Prompt: content stored in `description`
     * - Journal: content stored in `description`, metadata in archive_journals
     * - Other types: description serves as summary, content in extension tables
     */
    public function up(): void
    {
        Schema::create('archives', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('user_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->longText('content')->nullable();
            $table->boolean('is_favorite')->default(false);
            $table->softDeletes();
            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'type']);
            $table->index(['user_id', 'created_at']);
            $table->index(['user_id', 'is_favorite']);
            $table->index(['user_id', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('archives');
    }
};

<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('archive_websites', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreign('id')->references('id')->on('archives')->cascadeOnDelete();
            $table->text('url');
            $table->string('domain')->nullable();
            $table->text('feed_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('archive_websites');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('albums', function (Blueprint $table) {
            $table->id();
            $table->string('title', 150);
            $table->string('artist', 150);
            $table->string('genre', 80);
            $table->string('edition', 150)->nullable();
            $table->date('release_date');
            $table->text('description');
            $table->text('sound_notes')->nullable();
            $table->json('tracks')->nullable();
            $table->string('cover_path');
            $table->timestamps();
            $table->softDeletes(); // Удаление заполняет deleted_at, а сама запись остаётся.
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('albums');
    }
};

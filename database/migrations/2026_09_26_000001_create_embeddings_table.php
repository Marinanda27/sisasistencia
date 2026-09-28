<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('embeddings', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->unsignedInteger('person_id');
            $table->json('embedding');
            $table->string('etiqueta', 30)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('person_id', 'emb_person_id_foreign')
                ->references('id')
                ->on('person')
                ->onDelete('restrict')
                ->onUpdate('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('embeddings');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assistance', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->unsignedInteger('person_id');
            $table->dateTime('dateregister')->useCurrent()->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('person_id', 'fk_asistencias_persona')
                ->references('id')
                ->on('person')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assistance');
    }
};

<?php

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
        Schema::create('user', function (Blueprint $table) {
            $table->increments('id');
            $table->string('login', 20)->unique();
            $table->string('password');
            $table->char('state', 1)->default('H');
            $table->integer('usertype_id')->unsigned();
            $table->integer('person_id')->unsigned();
            $table->integer('branchoffice_id')->unsigned()->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('usertype_id')->references('id')->on('usertype')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('person_id')->references('id')->on('person')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('branchoffice_id')->references('id')->on('branchoffice')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

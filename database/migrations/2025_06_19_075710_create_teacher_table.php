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
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); 

            $table->string('name');
            $table->string('ic');
            $table->string('gender');
            $table->date('birth_date');
            $table->string('phone_number');
             $table->string('street');
            $table->string('area');
            $table->string('city');
            $table->string('state');
            $table->string('zip');
            $table->string('education_level');
            $table->string('field_of_expertise');
            $table->integer('experience_years');
            $table->text('experience_details');
            $table->string('qr_code')->nullable();
            $table->string('avatar')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher');
    }
};

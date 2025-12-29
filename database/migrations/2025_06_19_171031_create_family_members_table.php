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
        Schema::create('family_members', function (Blueprint $table) {
            $table->id();

            // Foreign key to students
            $table->foreignId('student_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('beneficiary_id')->nullable()->constrained()->onDelete('cascade');

            $table->string('name');
            $table->date('birth_date');
            $table->string('occupation');
            $table->string('relationship');
            // $table->string('other_relationship')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('family_members');
    }
};

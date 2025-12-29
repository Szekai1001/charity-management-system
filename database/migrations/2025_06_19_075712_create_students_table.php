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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guardian_id')->constrained()->onDelete('cascade'); // ✅ foreign key
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('teacher_id')->nullable()->constrained()->onDelete('cascade');      // if needed

            // Personal Details
            $table->string('name');
            $table->string('ic');
            $table->string('gender');
            $table->date('birth_date');
            $table->string('grade');
            $table->string('religion');
            $table->string('school');
            $table->string('phone');
            $table->string('street');
            $table->string('area');
            $table->string('city');
            $table->string('state');
            $table->string('zip');

            // Living Conditions
            $table->string('residential'); // Own House, Rent, etc.
            $table->string('family_income');
            $table->string('assist_from_child');
            $table->string('government_assist');
            $table->string('insurance_pay');
            $table->string('mortgage_expense');
            $table->string('transport_loan');
            $table->string('utility_expense');
            $table->string('education_expense');
            $table->string('family_expense');

            // Optional reason
            $table->text('reason')->nullable();

            // Amenities (optional: store as JSON)
            $table->json('amenities')->nullable();
            $table->string('qr_code')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};

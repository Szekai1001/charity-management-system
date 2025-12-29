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
        Schema::create('beneficiaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->string('name');
            $table->string('ic');
            $table->string('gender', 10);
            $table->date('birth_date');
            $table->string('religion');
            $table->string('family_role');
            $table->string('phone_number');
            $table->string('street');
            $table->text('area');
            $table->text('city');
            $table->text('state');
            $table->text('zip');
            $table->string('occupation');


            // Living Conditions
            $table->string('residential_status'); // Own House, Rent, etc.
            $table->string('family_income');
            $table->string('assist_from_child');
            $table->string('government_assist');
            $table->string('insurance_pay');
            $table->string('mortgage_expense');
            $table->string('transport_loan');
            $table->string('utility_expense');
            $table->string('education_expense');
            $table->string('family_expense');
            $table->json('basic_amenities_access')->nullable();
            $table->text('application_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beneficiary');
    }
};

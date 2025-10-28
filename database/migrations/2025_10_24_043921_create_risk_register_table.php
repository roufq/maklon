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
        Schema::create('risk_register', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('probability', ['very_low', 'low', 'medium', 'high', 'very_high']);
            $table->enum('impact', ['very_low', 'low', 'medium', 'high', 'very_high']);
            $table->enum('status', ['identified', 'assessed', 'mitigated', 'closed', 'occurred']);
            $table->text('mitigation_plan')->nullable();
            $table->text('contingency_plan')->nullable();
            $table->date('due_date')->nullable();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('identified_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risk_register');
    }
};

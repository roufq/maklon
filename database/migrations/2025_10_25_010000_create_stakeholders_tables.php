<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stakeholders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('role')->nullable();
            $table->enum('influence_level', ['very_low','low','medium','high','very_high'])->default('medium');
            $table->enum('interest_level', ['very_low','low','medium','high','very_high'])->default('medium');
            $table->text('communication_plan')->nullable();
            $table->timestamps();
        });

        Schema::create('stakeholder_comms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stakeholder_id')->constrained('stakeholders')->cascadeOnDelete();
            $table->string('subject');
            $table->text('notes')->nullable();
            $table->dateTime('planned_at')->nullable();
            $table->dateTime('sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stakeholder_comms');
        Schema::dropIfExists('stakeholders');
    }
};


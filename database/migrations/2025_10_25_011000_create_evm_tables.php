<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('project_baselines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->date('baseline_date');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('evm_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('baseline_id')->nullable()->constrained('project_baselines')->nullOnDelete();
            $table->date('as_of_date');
            $table->decimal('pv', 14, 2)->default(0);
            $table->decimal('ev', 14, 2)->default(0);
            $table->decimal('ac', 14, 2)->default(0);
            $table->timestamps();
            $table->unique(['project_id','as_of_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evm_points');
        Schema::dropIfExists('project_baselines');
    }
};


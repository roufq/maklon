<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->decimal('total_budget', 14, 2);
            $table->decimal('spent_amount', 14, 2)->default(0);
            $table->string('currency', 10)->default('IDR');
            $table->decimal('threshold_warning_percent', 5, 2)->default(80);
            $table->decimal('threshold_critical_percent', 5, 2)->default(100);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_budgets');
    }
};


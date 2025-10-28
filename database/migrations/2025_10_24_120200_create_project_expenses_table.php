<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('budget_category_id')->nullable()->constrained('budget_categories')->nullOnDelete();
            $table->foreignId('entered_by')->constrained('users')->onDelete('cascade');
            $table->decimal('amount', 14, 2);
            $table->date('spent_at');
            $table->boolean('billable')->default(false);
            $table->string('currency', 10)->default('IDR');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_expenses');
    }
};


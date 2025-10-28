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
        Schema::create('resource_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('task_id')->nullable()->constrained()->onDelete('cascade');
            $table->decimal('allocated_hours', 8, 2);
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('allocation_type', ['planned', 'actual', 'forecast'])->default('planned');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['project_id', 'user_id', 'task_id', 'start_date'], 'unique_allocation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resource_allocations');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable()->index();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('requested_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('status')->default('open'); // queued|open|in_progress|closed
            $table->string('priority')->default('normal'); // low|normal|high|urgent
            $table->timestamps();

            $table->index(['tenant_id','project_id']);
            $table->index(['tenant_id','status']);
            $table->index(['project_id','status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};


<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('production_batches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable()->index();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('bpom_registration_id')->nullable()->constrained('bpom_registrations')->nullOnDelete();
            $table->string('batch_number');
            $table->integer('quantity_produced')->default(0);
            $table->date('expiry_date')->nullable();
            $table->enum('qc_status', ['pending','passed','failed'])->default('pending')->index();
            $table->timestamps();
            $table->unique(['tenant_id','batch_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_batches');
    }
};


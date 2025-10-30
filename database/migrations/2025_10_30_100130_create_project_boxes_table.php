<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_boxes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable()->index();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('box_type_id')->constrained('box_types')->cascadeOnDelete();
            $table->string('size')->nullable(); // e.g., 10x10x5 cm
            $table->string('shape')->nullable(); // e.g., rectangular, cylindrical
            $table->string('mockup_path')->nullable();
            $table->timestamps();

            $table->index(['tenant_id','project_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_boxes');
    }
};


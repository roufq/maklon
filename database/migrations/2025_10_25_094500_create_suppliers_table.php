<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable()->index();
            $table->string('name');
            $table->string('type')->nullable(); // raw_material, packaging, logistics, etc.
            $table->boolean('bpom_certified')->default(false);
            $table->json('contact_info')->nullable(); // {email, phone, address}
            $table->unsignedTinyInteger('rating')->nullable(); // 1..5
            $table->decimal('performance_score', 5, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};


<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bpom_registrations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable()->index();
            $table->string('product_name');
            $table->string('registration_number')->unique();
            $table->date('approval_date')->nullable();
            $table->date('expiry_date')->nullable()->index();
            $table->enum('status', ['draft','active','expired','revoked','pending'])->default('pending')->index();
            $table->string('document_path')->nullable(); // stored in private disk
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bpom_registrations');
    }
};


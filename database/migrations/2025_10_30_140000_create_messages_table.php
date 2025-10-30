<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable()->index();
            $table->string('context_type'); // App\Models\Ticket or App\Models\Project
            $table->unsignedBigInteger('context_id');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('body')->nullable();
            $table->string('attachment_path')->nullable();
            $table->timestamps();

            $table->index(['tenant_id','context_type','context_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};


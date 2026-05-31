<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_template_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('template_id');
            $table->unsignedBigInteger('version_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamp('downloaded_at');
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
            $table->index(['template_id', 'version_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_template_logs');
    }
};



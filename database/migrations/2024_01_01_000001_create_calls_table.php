<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calls', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->string('original_filename');
            $table->string('file_path');
            $table->string('mime_type')->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->integer('duration_seconds')->nullable();

            $table->text('transcript')->nullable();
            $table->text('summary')->nullable();
            $table->unsignedTinyInteger('lead_score')->nullable();
            $table->enum('eligibility', ['yes', 'no', 'pending'])->default('pending');
            $table->json('next_actions')->nullable();
            $table->json('keywords_detected')->nullable();
            $table->string('sentiment')->nullable();
            $table->decimal('sentiment_score', 5, 2)->nullable();

            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamp('processed_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calls');
    }
};


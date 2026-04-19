<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_resources', function (Blueprint $table) {
            $table->id();
            $table->string('section', 32)->index();
            $table->string('title');
            $table->longText('body_markdown')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('member_resource_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_resource_id')->constrained('member_resources')->cascadeOnDelete();
            $table->string('original_name');
            $table->string('stored_path');
            $table->string('mime')->nullable();
            $table->unsignedBigInteger('size_bytes')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_resource_files');
        Schema::dropIfExists('member_resources');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('app_contents', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // privacy_policy / terms_of_use
            $table->string('title_ar');
            $table->string('title_en')->nullable();
            $table->longText('content_ar');
            $table->longText('content_en')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_contents');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('dynamic_fields', function (Blueprint $table) {
            $table->id();

            $table->foreignId('category_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('subcategory_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('label_ar');
            $table->string('label_en')->nullable();
            $table->string('key')->unique();

            $table->enum('type', [
                'text',
                'textarea',
                'number',
                'select',
                'boolean',
                'date'
            ]);

            $table->boolean('is_required')->default(false);
            $table->json('options')->nullable(); // للـ select
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dynamic_fields');
    }
};
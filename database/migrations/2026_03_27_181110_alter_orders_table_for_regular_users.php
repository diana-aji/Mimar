<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->nullable()
                ->after('service_id')
                ->constrained('users')
                ->cascadeOnDelete();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['sender_business_account_id']);
        });

        DB::statement('ALTER TABLE orders MODIFY sender_business_account_id BIGINT UNSIGNED NULL');

        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('sender_business_account_id')
                ->references('id')
                ->on('business_accounts')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['sender_business_account_id']);
        });

        DB::statement('ALTER TABLE orders MODIFY sender_business_account_id BIGINT UNSIGNED NOT NULL');

        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('sender_business_account_id')
                ->references('id')
                ->on('business_accounts')
                ->restrictOnDelete();
        });
    }
};
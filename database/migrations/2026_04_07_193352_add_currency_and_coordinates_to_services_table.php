<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            if (! Schema::hasColumn('services', 'currency')) {
                $table->string('currency', 10)->default('SYP')->after('price');
            }

            if (! Schema::hasColumn('services', 'latitude')) {
                $table->decimal('latitude', 10, 7)->nullable()->after('description');
            }

            if (! Schema::hasColumn('services', 'longitude')) {
                $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            }
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $columns = [];

            if (Schema::hasColumn('services', 'currency')) {
                $columns[] = 'currency';
            }

            if (Schema::hasColumn('services', 'latitude')) {
                $columns[] = 'latitude';
            }

            if (Schema::hasColumn('services', 'longitude')) {
                $columns[] = 'longitude';
            }

            if (! empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
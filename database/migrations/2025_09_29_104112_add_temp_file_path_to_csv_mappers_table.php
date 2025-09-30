<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('csv_mappers', function (Blueprint $table) {
        $table->string('temp_file_path')->nullable()->after('column_mapping');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('csv_mappers', function (Blueprint $table) {
            $table->dropColumn('temp_file_path');
        });
    }
};

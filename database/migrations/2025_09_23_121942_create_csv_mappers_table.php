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
        Schema::create('csv_mappers', function (Blueprint $table) {
            $table->id();

            // For future user support (nullable for now)
            $table->unsignedBigInteger('user_id')->nullable();

            // Mapping like: {0: "Date", 1: "Description", 2: "Amount", 3: "Ignore"}
            $table->json('column_mapping');

            // Optional: to support multiple saved mappers later
            $table->boolean('is_default')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('csv_mappers');
    }
};

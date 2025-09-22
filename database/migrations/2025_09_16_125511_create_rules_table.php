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
        Schema::create('rules', function (Blueprint $table) {
            $table->id();
            $table->string('field')->default('default_value'); // Default value for 'field'
            $table->string('match_type')->default('equals'); // Default value for 'match_type'
            $table->string('value')->default('default_value'); // Default value for 'value'
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('user_id')->nullable(); // Allow 'user_id' to be nullable
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rules');
    }
};

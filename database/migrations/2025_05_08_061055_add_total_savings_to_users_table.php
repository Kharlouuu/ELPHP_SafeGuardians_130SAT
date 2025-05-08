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
        // Create the savings table
        Schema::create('savings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('name');
            $table->decimal('amount', 10, 2);
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Add total_savings column to users table
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('total_savings', 10, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove total_savings column from users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('total_savings');
        });

        // Drop the savings table
        Schema::dropIfExists('savings');
    }
};
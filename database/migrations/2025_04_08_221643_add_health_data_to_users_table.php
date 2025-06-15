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
        Schema::table('users', function (Blueprint $table) {
            $table->integer('blood_pressure')->nullable()->after('remember_token');
            $table->decimal('blood_sugar', 5, 2)->nullable()->after('blood_pressure');
            $table->integer('cholesterol')->nullable()->after('blood_sugar');
            $table->integer('age')->nullable();
            $table->string('gender')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['blood_pressure', 'cholesterol', 'blood_sugar', 'age', 'gender']);
        });
    }
};

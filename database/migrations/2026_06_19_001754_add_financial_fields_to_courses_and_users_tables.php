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
        Schema::table('courses', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->default(0.00)->after('status');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->decimal('balance', 10, 2)->default(0.00)->after('password');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('price');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('balance');
        });
    }
};

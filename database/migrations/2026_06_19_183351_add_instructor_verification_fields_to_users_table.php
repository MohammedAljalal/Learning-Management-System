<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('bio')->nullable()->after('avatar');
            $table->string('expertise')->nullable()->after('bio');
            $table->string('phone')->nullable()->after('expertise');
            // ID verification images (stored as media library paths or raw paths)
            $table->string('id_front_path')->nullable()->after('phone');
            $table->string('id_back_path')->nullable()->after('id_front_path');
            $table->string('selfie_path')->nullable()->after('id_back_path');
            // Admin rejection reason
            $table->text('rejection_reason')->nullable()->after('selfie_path');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'bio', 'expertise', 'phone',
                'id_front_path', 'id_back_path', 'selfie_path',
                'rejection_reason',
            ]);
        });
    }
};

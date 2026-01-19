<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 20)->nullable()->after('email');
            $table->string('avatar', 500)->nullable()->after('phone');
            $table->string('status', 20)->default('active')->after('avatar');
            $table->softDeletes()->after('updated_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn(['phone', 'avatar', 'status']);
        });
    }
};

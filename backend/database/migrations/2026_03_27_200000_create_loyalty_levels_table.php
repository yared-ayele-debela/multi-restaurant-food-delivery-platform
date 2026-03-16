<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loyalty_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('slug', 50)->unique();
            $table->unsignedInteger('min_points');
            $table->unsignedInteger('max_points')->nullable();
            $table->decimal('cashback_rate', 5, 2)->default(0);
            $table->boolean('free_delivery')->default(false);
            $table->decimal('multiplier', 3, 2)->default(1);
            $table->string('badge_color', 20)->nullable();
            $table->string('icon', 500)->nullable();
            $table->json('benefits')->nullable();
            $table->timestamps();

            $table->index(['min_points', 'max_points']);
        });

        DB::table('loyalty_levels')->insert([
            [
                'id' => 1,
                'name' => 'Bronze',
                'slug' => 'bronze',
                'min_points' => 0,
                'max_points' => 999,
                'cashback_rate' => 1.00,
                'free_delivery' => false,
                'multiplier' => 1.00,
                'badge_color' => '#CD7F32',
                'icon' => null,
                'benefits' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Silver',
                'slug' => 'silver',
                'min_points' => 1000,
                'max_points' => 4999,
                'cashback_rate' => 2.50,
                'free_delivery' => false,
                'multiplier' => 1.25,
                'badge_color' => '#C0C0C0',
                'icon' => null,
                'benefits' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_levels');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('name');
            $table->text('description')->nullable()->after('slug');
            $table->string('image', 500)->nullable()->after('description');
            $table->boolean('is_active')->default(true)->after('sort_order');
            $table->softDeletes();
        });

        foreach (DB::table('categories')->orderBy('id')->cursor() as $row) {
            DB::table('categories')->where('id', $row->id)->update([
                'slug' => Str::slug($row->name.'-'.$row->id),
            ]);
        }

        Schema::table('categories', function (Blueprint $table) {
            $table->unique(['restaurant_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropUnique(['restaurant_id', 'slug']);
            $table->dropSoftDeletes();
            $table->dropColumn(['slug', 'description', 'image', 'is_active']);
        });
    }
};

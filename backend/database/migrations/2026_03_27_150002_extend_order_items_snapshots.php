<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->string('product_name')->nullable()->after('product_size_id');
            $table->string('product_size_name', 100)->nullable()->after('product_name');
            $table->json('addons')->nullable()->after('quantity');
            $table->decimal('addons_total', 10, 2)->default(0)->after('addons');
            $table->decimal('subtotal', 10, 2)->nullable()->after('addons_total');
        });

        if (Schema::hasColumn('order_items', 'line_total')) {
            foreach (DB::table('order_items')->orderBy('id')->cursor() as $row) {
                DB::table('order_items')->where('id', $row->id)->update([
                    'subtotal' => $row->line_total,
                ]);
            }
            Schema::table('order_items', function (Blueprint $table) {
                $table->dropColumn('line_total');
            });
        }
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('line_total', 10, 2)->default(0);
        });

        foreach (DB::table('order_items')->orderBy('id')->cursor() as $row) {
            DB::table('order_items')->where('id', $row->id)->update([
                'line_total' => $row->subtotal ?? 0,
            ]);
        }

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn([
                'product_name',
                'product_size_name',
                'addons',
                'addons_total',
                'subtotal',
            ]);
        });
    }
};

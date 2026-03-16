<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('order_number', 30)->nullable()->unique()->after('id');
            $table->foreignId('branch_id')->nullable()->after('restaurant_id')->constrained('restaurant_branches')->nullOnDelete();
            $table->foreignId('address_id')->nullable()->after('branch_id')->constrained('user_addresses')->nullOnDelete();
            $table->unsignedBigInteger('coupon_id')->nullable()->after('address_id');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('subtotal');
            $table->decimal('tax_amount', 10, 2)->default(0)->after('delivery_fee');
            $table->decimal('tax_rate', 5, 2)->default(0)->after('tax_amount');
            $table->decimal('commission_rate', 5, 2)->default(0)->after('total');
            $table->decimal('commission_amount', 10, 2)->default(0)->after('commission_rate');
            $table->decimal('restaurant_earnings', 10, 2)->default(0)->after('commission_amount');
            $table->decimal('driver_earnings', 10, 2)->default(0)->after('restaurant_earnings');
            $table->string('payment_method', 20)->default('cash')->after('driver_earnings');
            $table->string('payment_status', 20)->default('pending')->after('payment_method');
            $table->string('stripe_payment_intent_id')->nullable()->after('payment_status');
            $table->softDeletes();
        });

        foreach (DB::table('orders')->orderBy('id')->cursor() as $order) {
            DB::table('orders')->where('id', $order->id)->update([
                'order_number' => 'ORD-LEGACY-'.str_pad((string) $order->id, 8, '0', STR_PAD_LEFT),
                'commission_rate' => 15,
                'commission_amount' => 0,
                'restaurant_earnings' => 0,
                'driver_earnings' => 0,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropConstrainedForeignId('branch_id');
            $table->dropConstrainedForeignId('address_id');
            $table->dropColumn([
                'order_number',
                'coupon_id',
                'discount_amount',
                'tax_amount',
                'tax_rate',
                'commission_rate',
                'commission_amount',
                'restaurant_earnings',
                'driver_earnings',
                'payment_method',
                'payment_status',
                'stripe_payment_intent_id',
            ]);
        });
    }
};

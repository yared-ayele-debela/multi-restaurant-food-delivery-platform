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
            $table->timestamp('accepted_at')->nullable()->after('placed_at');
            $table->timestamp('preparing_at')->nullable()->after('accepted_at');
            $table->timestamp('ready_at')->nullable()->after('preparing_at');
            $table->timestamp('picked_up_at')->nullable()->after('ready_at');
            $table->timestamp('delivered_at')->nullable()->after('picked_up_at');
            $table->timestamp('completed_at')->nullable()->after('delivered_at');
            $table->timestamp('cancelled_at')->nullable()->after('completed_at');
            $table->text('cancellation_reason')->nullable()->after('cancelled_at');
            $table->foreignId('cancelled_by')->nullable()->after('cancellation_reason')->constrained('users')->nullOnDelete();
        });

        DB::table('orders')->where('status', 'out_for_delivery')->update(['status' => 'on_the_way']);
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('cancelled_by');
            $table->dropColumn([
                'accepted_at',
                'preparing_at',
                'ready_at',
                'picked_up_at',
                'delivered_at',
                'completed_at',
                'cancelled_at',
                'cancellation_reason',
            ]);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained('drivers')->nullOnDelete();
            $table->decimal('pickup_latitude', 10, 8);
            $table->decimal('pickup_longitude', 11, 8);
            $table->decimal('dropoff_latitude', 10, 8);
            $table->decimal('dropoff_longitude', 11, 8);
            $table->decimal('distance_km', 8, 2)->nullable();
            $table->unsignedInteger('estimated_time_minutes')->nullable();
            $table->unsignedInteger('actual_time_minutes')->nullable();
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->decimal('driver_earning', 10, 2)->default(0);
            $table->decimal('tip_amount', 10, 2)->default(0);
            $table->string('status', 32)->default('pending')->index();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('picked_up_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->text('failed_reason')->nullable();
            $table->unsignedTinyInteger('driver_rating')->nullable();
            $table->text('customer_feedback')->nullable();
            $table->timestamps();

            $table->unique('order_id');
            $table->index('driver_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('vehicle_type', 20)->default('motorcycle');
            $table->string('vehicle_number', 50)->nullable();
            $table->string('license_number', 100)->nullable();
            $table->string('license_image', 500)->nullable();
            $table->string('insurance_document', 500)->nullable();
            $table->boolean('is_available')->default(false);
            $table->boolean('is_approved')->default(false);
            $table->boolean('is_on_delivery')->default(false);
            $table->decimal('current_latitude', 10, 8)->nullable();
            $table->decimal('current_longitude', 11, 8)->nullable();
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->unsignedInteger('total_ratings')->default(0);
            $table->unsignedInteger('total_deliveries')->default(0);
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};

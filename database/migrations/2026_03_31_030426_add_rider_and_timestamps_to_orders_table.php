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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('order_id')->unique()->nullable()->after('id');
            $table->unsignedBigInteger('rider_id')->nullable()->after('customer_id');
            $table->integer('delivery_step')->default(0)->after('status');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('picked_up_at')->nullable();
            $table->timestamp('in_transit_at')->nullable();
            $table->timestamp('arrived_at')->nullable();
            $table->timestamp('completed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'order_id', 'rider_id', 'delivery_step',
                'accepted_at', 'picked_up_at', 'in_transit_at', 'arrived_at', 'completed_at'
            ]);
        });
    }
};

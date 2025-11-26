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
        Schema::table('products_orders', function (Blueprint $table) {
            $table->float('delivery_cost', 1)->nullable()->after('total');
            $table->string('lon')->nullable()->after('delivery_cost');
            $table->string('lat')->nullable()->after('lon');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products_orders', function (Blueprint $table) {
            $table->dropColumn([
                'delivery_cost', 'lon', 'lat'
            ]);
        });
    }
};

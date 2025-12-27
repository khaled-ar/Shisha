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
            $table->foreignId('employee_id')->nullable()->after('status')->constrained('employees')->nullOnDelete()->cascadeOnUpdate();
            $table->timestamp('confirmed_at')->nullable()->after('employee_id');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products_orders', function (Blueprint $table) {
            $table->dropColumn(['employee_id', 'confirmed_at']);
        });
    }
};

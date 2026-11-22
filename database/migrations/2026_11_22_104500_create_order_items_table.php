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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->constrained('products')
                ->restrictOnDelete();

            $table->string('product_name_snapshot');
            $table->string('sku_snapshot');
            $table->string('condition_snapshot');
            $table->string('condition_grade_snapshot')->nullable();
            $table->unsignedTinyInteger('battery_health_snapshot')->nullable();
            $table->string('functional_status_snapshot')->nullable();
            $table->text('defect_notes_snapshot')->nullable();
            $table->text('included_accessories_snapshot')->nullable();
            $table->string('warranty_info_snapshot')->nullable();

            $table->decimal('unit_price_snapshot', 12, 2);
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('line_total', 12, 2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};

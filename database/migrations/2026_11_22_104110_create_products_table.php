<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->foreignId('category_id')
                ->constrained('categories')
                ->restrictOnDelete();

            $table->string('name');
            $table->string('model');
            $table->string('sku')->unique();
            $table->string('storage')->nullable();
            $table->string('color')->nullable();
            $table->text('description')->nullable();

            $table->enum('condition', ['new', 'used']);
            $table->decimal('price', 12, 2);
            $table->unsignedInteger('stock')->default(0);

            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);

            $table->enum('condition_grade', ['A', 'B', 'C'])->nullable();
            $table->unsignedTinyInteger('battery_health')->nullable();
            $table->string('functional_status')->nullable();
            $table->text('defect_notes')->nullable();
            $table->text('included_accessories')->nullable();
            $table->string('warranty_info')->nullable();

            $table->timestamps();
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE products ADD CONSTRAINT check_product_price CHECK (price > 0)');
            DB::statement('ALTER TABLE products ADD CONSTRAINT check_product_battery CHECK (battery_health IS NULL OR battery_health <= 100)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

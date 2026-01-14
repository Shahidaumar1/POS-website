<?php
// Example: create_products_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   // create_products_table migration
public function up()
{
    Schema::create('products', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->decimal('price', 10, 2);
        $table->integer('stock_quantity');
        $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
        $table->string('barcode')->nullable(); // For barcode scanning
        $table->timestamps();
    });
}


    public function down()
    {
        Schema::dropIfExists('products');
    }
};

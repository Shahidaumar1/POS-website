<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // Define the table if it's not the plural of the model name
    protected $table = 'products';

    // Define the fillable attributes
    protected $fillable = ['name', 'price', 'stock_quantity', 'category_id', 'barcode'];

    // Define relationship with Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Define relationship with SaleItems
    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }
}

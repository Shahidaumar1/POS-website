<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    // Define the table name if necessary
    protected $table = 'sales';

    // Define the fillable attributes
    protected $fillable = ['user_id', 'total_amount', 'payment_method'];

    // Define relationships if needed
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }
}


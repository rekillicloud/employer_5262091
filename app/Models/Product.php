<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Filterable;
use App\Traits\Sortable;

class Product extends Model
{
    use Filterable,
        Sortable, 
        HasFactory;

    protected $fillable = ['name', 'price', 'category_id', 'in_stock', 'rating'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Filterable;

class Product extends Model
{
    use Filterable;

    protected $fillable = ['name', 'price', 'category_id', 'in_stock', 'rating'];

    protected array $searchableFields = ['name'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}

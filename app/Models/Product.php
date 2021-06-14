<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'products';
    protected $fillable = ['name', 'description', 'category', 'price', 'stock'];

    /**
     * @return HasOne
     */
    public function getCategory(): HasOne
    {
        return $this->hasOne(Category::class, 'id', 'category');
    }
}

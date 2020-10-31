<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends App
{
    protected $guarded = ['id', 'created_at', 'updated_at']; // Запрещается редактировать
    //protected $fillable = ['title', 'price', 'description'];  // Разрешается редактировать


    use SoftDeletes;


    // Связь многие ко многим
    public function category()
    {
        return $this->belongsToMany(Category::class, 'category_product');
    }

    public function orderProduct()
    {
        return $this->hasMany(OrderProduct::class, 'product_id');
    }

    // Связь многие ко многим
    public function filter_values()
    {
        return $this->belongsToMany(Filter::class, 'filter_products');
    }
}

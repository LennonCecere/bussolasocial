<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductModel extends Model
{
    use SoftDeletes, hasFactory;

    protected $table = 'product';
    protected $guarded = [];
    protected $dates = ['created_at','updated_at', 'deleted_at'];
    public $timestamps = true;
    protected $fillable = ['name', 'description', 'price_in_cents', 'quantity_in_stock'];

    public function cart()
    {
        return $this->hasMany(CartModel::class, 'product_id', 'id');
    }
}

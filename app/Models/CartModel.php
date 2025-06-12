<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartModel extends Model
{
    protected $table = 'cart';
    protected $guarded = [];
    protected $dates = ['created_at','updated_at'];
    public $timestamps = true;
    protected $fillable = ['user_id', 'product_id', 'quantity'];

    /**
     * @return BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(ProductModel::class, 'product_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'id');
    }
}

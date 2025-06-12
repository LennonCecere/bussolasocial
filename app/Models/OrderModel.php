<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderModel extends Model
{
    protected $table = 'order';
    protected $guarded = [];
    protected $dates = ['created_at','updated_at'];
    public $timestamps = true;
    protected $fillable = ['user_id', 'products', 'amount_in_cents', 'payment_type_id'];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function paymentType(): BelongsTo
    {
        return $this->belongsTo(PaymentTypeModel::class, 'payment_type_id', 'id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentTypeModel extends Model
{
    use SoftDeletes, hasFactory;

    protected $table = 'payment_type';
    protected $guarded = [];
    protected $dates = ['created_at','updated_at', 'deleted_at'];
    public $timestamps = true;
    protected $fillable = ['name', 'description', 'installments'];
}

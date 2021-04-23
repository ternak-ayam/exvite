<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentDetail extends Model
{
    use HasFactory;
    protected $primaryKey = 'payment_id';
    protected $table = 'payment_details';
    protected $fillable = [
        'payment_id',
        'payment_method',
        'path',
        'discount',
        'admin_fee',
        'amount',
        'status',
        'total',
        'invoice',
    ];

    public function setTotal() {
        return $this->total = $this->amount + $this->admin_fee - $this->discount;
    }
    public function details() {
        return $this->hasMany(OrderDetails::class, 'payment_id');
    }
}

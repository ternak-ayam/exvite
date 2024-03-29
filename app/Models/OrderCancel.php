<?php

namespace App\Models;

use App\Events\OrderConfirm;
use App\Events\OrderUnConfirm;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderCancel extends Model
{
    use HasFactory;
    protected $fillable = [
        'customer_id',
        'studio_id',
        'order_id',
        'status',
    ];
    protected $dates = ['deleted_at'];

    protected $dispatchesEvents = [
        'created' => OrderUnConfirm::class,
    ];

    public function setGrowth() {
        $now = $this->where('studio_id', $this->studio_id)->whereDay('created_at', now()->day)->count();
        if($this->where('studio_id', $this->studio_id)->whereDay('created_at', now()->day - 1)->count() == 0) { 
            $yesterday = 0; 
            return  $now - $yesterday * 100;
        } else { 
            $yesterday = $this->where('studio_id', $this->studio_id)->whereDay('created_at', now()->day - 1)->count(); 
            return  $now - $yesterday / $yesterday * 100;
        }
    }
}

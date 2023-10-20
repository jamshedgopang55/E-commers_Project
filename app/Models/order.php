<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class order extends Model
{
    protected $table = 'order';

    use HasFactory;
    public function items(){
        return $this->hasMany(order_item::class);
    }
}

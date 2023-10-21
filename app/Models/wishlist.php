<?php

namespace App\Models;

use App\Models\product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class wishlist extends Model
{
    use HasFactory;
    public $fillable =['user_id','product_id'];
    public function product(){
        return $this->belongsTo(product::class);
    }
}

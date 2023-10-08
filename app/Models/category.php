<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\subCategory;

class category extends Model
{
    protected $table = 'categories';
    use HasFactory;
    public function subCategory(){
        return $this->hasMany(subCategory::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
   protected $table = 'tbl_sub_category';

    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
    ];
}

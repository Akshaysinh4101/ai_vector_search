<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryEmbedding extends Model
{
    protected $table = 'tbl_category_embedding';

    use HasFactory;

    protected $fillable = [
        'sub_category_id',
        'service',
        'keywords',
        'embedding',
        'row_hash',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Word extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'gif_path', 'category_id'];

    

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}

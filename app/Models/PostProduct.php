<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'product_id',
        'sort_order',
        'column_title',
        'column_subtitle',
        'column_image',
        'cta_text',
        'cta_url',
        'badge_text',
        'is_highlight'
    ];

    protected $casts = [
        'is_highlight' => 'boolean'
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function cells()
    {
        return $this->hasMany(ComparisonCell::class, 'post_product_id', 'id');
    }
}

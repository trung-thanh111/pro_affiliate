<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComparisonCell extends Model
{
    use HasFactory;

    protected $fillable = [
        'comparison_row_id',
        'post_product_id',
        'value_text',
        'value_html',
        'value_type',
        'is_highlight',
        'note',
        'icon',
        'link_url'
    ];

    protected $casts = [
        'is_highlight' => 'boolean'
    ];

    public function row()
    {
        return $this->belongsTo(ComparisonRow::class, 'comparison_row_id');
    }

    public function post_product()
    {
        return $this->belongsTo(PostProduct::class, 'post_product_id');
    }
}

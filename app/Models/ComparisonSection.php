<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComparisonSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'title',
        'description',
        'sort_order'
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function rows()
    {
        return $this->hasMany(ComparisonRow::class, 'comparison_section_id', 'id')->orderBy('sort_order');
    }
}

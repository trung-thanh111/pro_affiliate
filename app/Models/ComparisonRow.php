<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComparisonRow extends Model
{
    use HasFactory;

    protected $fillable = [
        'comparison_section_id',
        'label',
        'slug',
        'row_type',
        'unit',
        'tooltip',
        'sort_order',
        'is_featured'
    ];

    protected $casts = [
        'is_featured' => 'boolean'
    ];

    public function section()
    {
        return $this->belongsTo(ComparisonSection::class, 'comparison_section_id');
    }

    public function cells()
    {
        return $this->hasMany(ComparisonCell::class, 'comparison_row_id', 'id');
    }
}

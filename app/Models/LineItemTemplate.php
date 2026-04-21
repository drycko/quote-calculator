<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LineItemTemplate extends Model
{
    protected $fillable = [
        'name',
        'category',
        'template_type',
        'calculation_type',
        'default_rate',
        'default_percentage',
        'currency',
        'conversion_rate',
        'is_plugin',
        'default_notes',
    ];

    protected function casts(): array
    {
        return [
            'default_rate'       => 'decimal:2',
            'default_percentage' => 'decimal:2',
            'conversion_rate'    => 'decimal:2',
            'is_plugin'          => 'boolean',
        ];
    }
}

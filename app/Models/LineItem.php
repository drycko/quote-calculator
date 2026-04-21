<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LineItem extends Model
{
    protected $fillable = [
        'phase_id',
        'name',
        'rate',
        'quantity',
        'calculation_type',
        'percentage_value',
        'currency',
        'conversion_rate',
        'is_plugin',
        'notes',
        'total',
    ];

    protected function casts(): array
    {
        return [
            'rate'             => 'decimal:2',
            'quantity'         => 'decimal:2',
            'percentage_value' => 'decimal:2',
            'conversion_rate'  => 'decimal:2',
            'total'            => 'decimal:2',
            'is_plugin'        => 'boolean',
        ];
    }

    public function phase(): BelongsTo
    {
        return $this->belongsTo(Phase::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quote extends Model
{
    protected $fillable = [
        'user_id',
        'public_token',
        'client_name',
        'salesperson_name',
        'salesperson_email',
        'template_type',
        'apply_markup',
        'apply_vat',
        'subtotal',
        'main_total',
        'plugin_total',
        'markup_rate',
        'markup_amount',
        'total_ex_vat',
        'vat',
        'total_inc_vat',
        'snapshot',
    ];

    protected function casts(): array
    {
        return [
            'subtotal'       => 'decimal:2',
            'main_total'     => 'decimal:2',
            'plugin_total'   => 'decimal:2',
            'markup_rate'    => 'decimal:2',
            'markup_amount'  => 'decimal:2',
            'total_ex_vat'   => 'decimal:2',
            'vat'            => 'decimal:2',
            'total_inc_vat'  => 'decimal:2',
            'snapshot'       => 'array',
            'apply_markup'   => 'boolean',
            'apply_vat'      => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function phases(): HasMany
    {
        return $this->hasMany(Phase::class);
    }
}

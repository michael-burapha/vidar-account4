<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function defaultCurrency()
    {
        return $this->belongsTo(Currency::class, 'default_currency_id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /** Foreign clients are an export of services (no UZ VAT, EEISVO registration). */
    public function isExport(): bool
    {
        return strtoupper($this->country) !== 'UZ';
    }

    public function getCountryNameAttribute(): string
    {
        return self::countries()[strtoupper($this->country)] ?? $this->country;
    }

    /** Countries Vidar Digital invoices into. */
    public static function countries(): array
    {
        return [
            'UZ' => 'Uzbekistan',
            'DK' => 'Denmark',
            'TH' => 'Thailand',
            'GB' => 'United Kingdom',
            'KZ' => 'Kazakhstan',
        ];
    }
}

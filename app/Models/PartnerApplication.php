<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnerApplication extends Model
{
    protected $fillable = [
        'contact_name',
        'email',
        'password',
        'agency_name',
        'address',
        'opening_hours',
        'whatsapp',
        'routes',
        'verification_token_hash',
        'expires_at',
    ];

    protected $hidden = [
        'password',
        'verification_token_hash',
    ];

    protected function casts(): array
    {
        return [
            'routes' => 'array',
            'expires_at' => 'datetime',
        ];
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Concert extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'starts_at',
        'ends_at',
        'venue_name',
        'venue_address',
        'city',
        'program',
        'description',
        'ticket_url',
        'is_published',
        'seo_title',
        'seo_description',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_published' => 'boolean',
        ];
    }
}

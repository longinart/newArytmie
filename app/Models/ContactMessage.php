<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'consented_to_processing',
        'is_read',
        'read_at',
        'responded_at',
    ];

    protected function casts(): array
    {
        return [
            'consented_to_processing' => 'boolean',
            'is_read' => 'boolean',
            'read_at' => 'datetime',
            'responded_at' => 'datetime',
        ];
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Photo extends Model
{
    use HasFactory;

    protected $fillable = [
        'album_id',
        'title',
        'alt_text',
        'image_path',
        'caption',
        'taken_at',
        'sort_order',
        'is_published',
    ];

    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class);
    }

    protected function casts(): array
    {
        return [
            'taken_at' => 'date',
            'is_published' => 'boolean',
        ];
    }
}

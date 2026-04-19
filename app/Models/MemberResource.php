<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MemberResource extends Model
{
    protected $fillable = [
        'section',
        'title',
        'body_markdown',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function files(): HasMany
    {
        return $this->hasMany(MemberResourceFile::class)->orderBy('sort_order');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberResourceFile extends Model
{
    protected $fillable = [
        'member_resource_id',
        'original_name',
        'stored_path',
        'mime',
        'size_bytes',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'size_bytes' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function resource(): BelongsTo
    {
        return $this->belongsTo(MemberResource::class, 'member_resource_id');
    }
}

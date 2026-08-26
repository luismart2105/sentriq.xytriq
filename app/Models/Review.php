<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'token',
        'client_name',
        'municipality',
        'service',
        'rating',
        'comment',
        'photo_path',
        'status',
        'submitted_at',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approved')->latest('approved_at');
    }

    public function publicName(): string
    {
        return str($this->client_name)->before(' ')->toString();
    }
}

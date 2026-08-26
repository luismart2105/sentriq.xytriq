<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Kit extends Model
{
    protected $fillable = [
        'service_slug',
        'name',
        'slug',
        'camera_count',
        'price',
        'description',
        'features',
        'installation_included',
        'featured',
        'active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'features' => 'array',
            'installation_included' => 'boolean',
            'featured' => 'boolean',
            'active' => 'boolean',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('active', true)->orderBy('sort_order')->orderBy('price');
    }
}

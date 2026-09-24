<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProspectActivity extends Model
{
    protected $fillable = [
        'prospect_id', 'user_id', 'type', 'summary', 'old_stage', 'new_stage',
        'next_action', 'next_action_at', 'happened_at',
    ];

    protected function casts(): array
    {
        return ['happened_at' => 'datetime', 'next_action_at' => 'datetime'];
    }

    public function prospect(): BelongsTo
    {
        return $this->belongsTo(Prospect::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

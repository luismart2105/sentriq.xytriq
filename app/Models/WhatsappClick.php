<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappClick extends Model
{
    protected $fillable = ['page', 'service', 'utm_source', 'utm_medium', 'utm_campaign', 'clicked_at'];

    protected function casts(): array
    {
        return ['clicked_at' => 'datetime'];
    }
}

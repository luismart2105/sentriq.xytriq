<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prospect extends Model
{
    public const REQUEST_TYPES = [
        'project' => 'Proyecto nuevo',
        'support' => 'Soporte o reparación',
    ];

    public const STAGES = [
        'new' => 'Nuevo',
        'contacted' => 'Contactado',
        'interested' => 'Interesado',
        'visit' => 'Visita',
        'quote' => 'Cotización',
        'won' => 'Ganado',
        'lost' => 'Perdido',
        'unqualified' => 'No calificado',
    ];

    public const SOURCES = [
        'web' => 'Web',
        'whatsapp' => 'WhatsApp',
        'facebook' => 'Facebook',
        'referral' => 'Recomendación',
        'prospecting' => 'Prospección',
        'other' => 'Otro',
    ];

    public const ACTIVITY_TYPES = [
        'note' => 'Nota',
        'call' => 'Llamada',
        'message' => 'Mensaje',
        'visit' => 'Visita',
        'follow_up' => 'Seguimiento',
        'stage_change' => 'Cambio de etapa',
    ];

    protected $fillable = [
        'name', 'business', 'request_type', 'phone', 'email', 'service_interest', 'municipality', 'description',
        'source', 'campaign', 'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content',
        'landing_page', 'referrer', 'stage', 'assigned_user_id', 'next_follow_up_at', 'last_contact_at',
    ];

    protected function casts(): array
    {
        return [
            'next_follow_up_at' => 'datetime',
            'last_contact_at' => 'datetime',
        ];
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(ProspectActivity::class)->orderByDesc('happened_at')->orderByDesc('id');
    }

    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class);
    }

    public function displayName(): string
    {
        return $this->business ?: ($this->name ?: 'Prospecto #'.$this->id);
    }

    public function whatsappUrl(): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $this->phone);

        if (! $digits) {
            return null;
        }

        if (strlen($digits) === 10) {
            $digits = '52'.$digits;
        }

        return 'https://wa.me/'.$digits.'?text='.rawurlencode('Hola '.($this->name ?: $this->business ?: '').', soy de Sentriq. Gracias por contactarnos.');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    protected $fillable = [
        'number', 'client_name', 'quote_date', 'validity_days', 'title', 'description',
        'items', 'installation_amount', 'equipment_warranty_duration', 'equipment_warranty_unit',
        'equipment_warranty', 'installation_warranty_duration', 'installation_warranty_unit', 'installation_warranty',
        'installation_scope', 'project_considerations', 'project_manager', 'signature_path',
        'signing_token', 'client_signer_name', 'client_signature_path', 'signed_at',
        'signed_ip', 'signed_user_agent', 'status',
    ];

    protected function casts(): array
    {
        return [
            'quote_date' => 'date',
            'items' => 'array',
            'installation_amount' => 'decimal:2',
            'signed_at' => 'datetime',
        ];
    }

    public function materialsSubtotal(): float
    {
        return collect($this->items)->sum(fn (array $item): float => (float) ($item['quantity'] ?? 0) * (float) ($item['unit_price'] ?? 0)
        );
    }

    public function total(): float
    {
        return $this->materialsSubtotal() + (float) $this->installation_amount;
    }

    public function warrantyLabel(string $type): string
    {
        $duration = (int) $this->getAttribute($type.'_warranty_duration');
        $unit = $this->getAttribute($type.'_warranty_unit');

        if ($duration === 0) {
            return 'Sin garantía';
        }

        $label = $unit === 'months'
            ? ($duration === 1 ? 'mes' : 'meses')
            : ($duration === 1 ? 'año' : 'años');

        return $duration.' '.$label.' de garantía';
    }

    public function signatureUrl(): ?string
    {
        return $this->signature_path ? asset($this->signature_path) : null;
    }

    public function clientSignatureUrl(): ?string
    {
        return $this->client_signature_path ? asset($this->client_signature_path) : null;
    }
}

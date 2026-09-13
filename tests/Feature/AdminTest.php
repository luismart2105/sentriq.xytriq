<?php

namespace Tests\Feature;

use App\Models\Kit;
use App\Models\Quote;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Tests\DatabaseTestCase;

class AdminTest extends DatabaseTestCase
{
    public function test_admin_area_requires_authentication(): void
    {
        $this->get('/admin')->assertRedirect('/admin/ingresar');
    }

    public function test_authenticated_admin_can_create_a_kit(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/admin/kits', [
            'name' => 'Kit de 4 cámaras',
            'camera_count' => 4,
            'price' => 8999.00,
            'description' => 'Solución residencial.',
            'features_text' => "4 cámaras\nGrabador\nDisco duro",
            'sort_order' => 1,
            'installation_included' => 1,
            'active' => 1,
        ]);

        $response->assertRedirect('/admin/kits');

        $kit = Kit::firstOrFail();
        $this->assertSame('kit-de-4-camaras', $kit->slug);
        $this->assertSame(['4 cámaras', 'Grabador', 'Disco duro'], $kit->features);
        $this->assertTrue($kit->active);

        $this->get('/servicios/camaras-de-seguridad')
            ->assertOk()
            ->assertSee('Kit de 4 cámaras')
            ->assertSee('$8,999.00', false);
    }

    public function test_authenticated_admin_can_create_and_print_a_quote(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->post('/admin/presupuestos', [
            'client_name' => 'Cliente Ejemplo',
            'quote_date' => '2026-09-11', 'validity_days' => 15,
            'title' => 'Presupuesto de acceso inteligente', 'status' => 'draft',
            'installation_amount' => 2000,
            'deposit_amount' => 5000,
            'equipment_warranty_duration' => 6, 'equipment_warranty_unit' => 'months',
            'installation_warranty_duration' => 0, 'installation_warranty_unit' => 'years',
            'installation_warranty' => 'La reparación se realiza sobre componentes existentes.',
            'items' => [['concept' => 'Videoportero', 'model' => 'DS-KV', 'benefit' => 'Acceso remoto', 'quantity' => 2, 'unit_price' => 1500]],
        ]);

        $quote = Quote::firstOrFail();
        $this->assertSame('COT-2026-0911-01', $quote->number);
        $response->assertRedirect(route('admin.quotes.edit', $quote));
        $this->assertSame(5000.0, $quote->total());
        $this->assertSame(0.0, $quote->remainingBalance());
        $this->get(route('admin.quotes.show', $quote))->assertOk()->assertSee('Cliente Ejemplo')->assertSee('$5,000.00', false);
        $this->assertSame('6 meses de garantía', $quote->warrantyLabel('equipment'));
        $this->assertSame('Sin garantía', $quote->warrantyLabel('installation'));
    }

    public function test_quote_numbers_are_consecutive_for_each_day(): void
    {
        $user = User::factory()->create();
        $payload = [
            'client_name' => 'Cliente', 'quote_date' => '2026-09-11', 'validity_days' => 15,
            'title' => 'Presupuesto', 'status' => 'draft', 'installation_amount' => 0,
            'equipment_warranty_duration' => 1, 'equipment_warranty_unit' => 'years',
            'installation_warranty_duration' => 1, 'installation_warranty_unit' => 'years',
            'items' => [['concept' => 'Equipo', 'quantity' => 1, 'unit_price' => 100]],
        ];

        $this->actingAs($user)->post('/admin/presupuestos', $payload)->assertRedirect();
        $this->actingAs($user)->post('/admin/presupuestos', $payload)->assertRedirect();
        $this->actingAs($user)->post('/admin/presupuestos', [...$payload, 'quote_date' => '2026-09-12'])->assertRedirect();

        $this->assertSame([
            'COT-2026-0911-01', 'COT-2026-0911-02', 'COT-2026-0912-01',
        ], Quote::orderBy('id')->pluck('number')->all());
    }

    public function test_admin_can_attach_a_png_signature_to_a_quote(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $payload = [
            'client_name' => 'Cliente', 'quote_date' => '2026-09-11', 'validity_days' => 15,
            'title' => 'Reparación', 'status' => 'draft', 'installation_amount' => 0,
            'equipment_warranty_duration' => 0, 'equipment_warranty_unit' => 'months',
            'equipment_warranty' => 'Equipo reparado sin garantía del fabricante.',
            'installation_warranty_duration' => 3, 'installation_warranty_unit' => 'months',
            'items' => [['concept' => 'Reparación', 'quantity' => 1, 'unit_price' => 500]],
            'signature_data' => 'data:image/png;base64,'.base64_encode(base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVQIHWP4z8DwHwAFgAI/ScL1WQAAAABJRU5ErkJggg==')),
        ];

        $this->actingAs($user)->post('/admin/presupuestos', $payload)->assertRedirect();
        $quote = Quote::firstOrFail();

        $this->assertNotNull($quote->signature_path);
        Storage::disk('public')->assertExists(str_replace('storage/', '', $quote->signature_path));
        $this->get(route('admin.quotes.show', $quote))->assertOk()->assertSee($quote->signatureUrl(), false);
    }

    public function test_client_can_sign_quote_using_private_link(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $quote = Quote::create([
            'number' => 'COT-2026-0911-01', 'client_name' => 'Cliente', 'quote_date' => '2026-09-11',
            'validity_days' => 15, 'title' => 'Reparación', 'status' => 'sent', 'items' => [['concept' => 'Servicio', 'quantity' => 1, 'unit_price' => 500]],
            'installation_amount' => 0, 'equipment_warranty_duration' => 0, 'equipment_warranty_unit' => 'months',
            'installation_warranty_duration' => 0, 'installation_warranty_unit' => 'months', 'signing_token' => str_repeat('a', 48),
        ]);

        $this->get(route('quotes.sign', $quote->signing_token))->assertOk()->assertSee('Firmar y autorizar presupuesto');
        $this->get(route('quotes.document', $quote->signing_token))->assertOk()->assertSee($quote->number);
        $this->post(route('quotes.sign.store', $quote->signing_token), [
            'client_signer_name' => 'Persona Autorizada', 'acceptance' => '1',
            'signature_data' => 'data:image/png;base64,'.base64_encode(base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVQIHWP4z8DwHwAFgAI/ScL1WQAAAABJRU5ErkJggg==')),
        ])->assertRedirect();

        $quote->refresh();
        $this->assertSame('accepted', $quote->status);
        $this->assertSame('Persona Autorizada', $quote->client_signer_name);
        $this->assertNotNull($quote->signed_at);
    }
}

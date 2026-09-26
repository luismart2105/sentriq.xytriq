<?php

namespace Tests\Feature;

use App\Models\Prospect;
use App\Models\Quote;
use App\Models\User;
use App\Models\WhatsappClick;
use App\Notifications\NewProspectNotification;
use App\Notifications\ProspectConfirmationNotification;
use Illuminate\Support\Facades\Notification;
use Tests\DatabaseTestCase;

class ProspectTest extends DatabaseTestCase
{
    public function test_public_form_creates_one_prospect_and_initial_activity(): void
    {
        config()->set('sentriq.leads.form_enabled', true);
        Notification::fake();
        $responsible = User::factory()->create(['email' => 'support@sentriq.xytriq.com']);

        $response = $this->post('/contacto', [
            'name' => 'María López',
            'request_type' => 'project',
            'phone' => '33 1234 5678',
            'email' => 'maria@example.com',
            'service_interest' => 'camaras-de-seguridad',
            'municipality' => 'Zapopan',
            'description' => 'Quiero revisar opciones para un local.',
            'privacy_accepted' => '1',
            'utm_source' => 'facebook',
            'utm_campaign' => 'prueba-14-dias',
            'landing_page' => 'https://sentriq.xytriq.com/contacto?utm_source=facebook',
            'referrer' => 'https://facebook.com/post?id=secreto',
        ]);

        $response->assertRedirect('/contacto/gracias');
        $this->assertDatabaseCount('prospects', 1);
        $prospect = Prospect::firstOrFail();
        $this->assertSame('web', $prospect->source);
        $this->assertSame('new', $prospect->stage);
        $this->assertSame('project', $prospect->request_type);
        $this->assertSame($responsible->id, $prospect->assigned_user_id);
        $this->assertSame('https://facebook.com/post', $prospect->referrer);
        $this->assertCount(1, $prospect->activities);
        Notification::assertSentOnDemand(NewProspectNotification::class, function (NewProspectNotification $notification, array $channels, object $notifiable): bool {
            return $notifiable->routes['mail'] === 'support@sentriq.xytriq.com';
        });
        Notification::assertSentOnDemand(ProspectConfirmationNotification::class, function (ProspectConfirmationNotification $notification, array $channels, object $notifiable): bool {
            return $notifiable->routes['mail'] === 'maria@example.com';
        });
    }

    public function test_confirmation_page_reassures_the_customer_without_being_indexed(): void
    {
        $this->get('/contacto/gracias')
            ->assertOk()
            ->assertSee('El siguiente paso corre por nuestra cuenta')
            ->assertSee('Te responderemos dentro de 24 horas hábiles')
            ->assertSee('noindex, follow', false);
    }

    public function test_public_form_validates_contact_consent_and_honeypot(): void
    {
        config()->set('sentriq.leads.form_enabled', true);

        $this->from('/contacto')->post('/contacto', [
            'name' => 'Robot', 'service_interest' => 'alarmas', 'municipality' => 'Guadalajara',
            'request_type' => 'support',
            'description' => 'Mensaje', 'website' => 'spam.example',
        ])->assertRedirect('/contacto')->assertSessionHasErrors(['phone', 'privacy_accepted', 'website']);

        $this->assertDatabaseCount('prospects', 0);
    }

    public function test_campaign_parameters_survive_navigation_to_contact(): void
    {
        config()->set('sentriq.leads.form_enabled', true);
        $this->get('/?utm_source=facebook&utm_campaign=septiembre')->assertOk();
        $this->get('/contacto')->assertOk()->assertSee('value="facebook"', false)->assertSee('value="septiembre"', false);
    }

    public function test_anonymous_visitors_cannot_access_prospects(): void
    {
        $this->get('/admin/prospectos')->assertRedirect('/admin/ingresar');
        $this->get('/admin/prospectos/1')->assertRedirect('/admin/ingresar');
        $this->post('/admin/prospectos', [])->assertRedirect('/admin/ingresar');
    }

    public function test_admin_can_create_update_and_schedule_a_prospect(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->post('/admin/prospectos', [
            'name' => 'Taller Norte', 'phone' => '3312345678', 'source' => 'whatsapp', 'stage' => 'new', 'request_type' => 'support',
            'service_interest' => 'alarmas',
        ])->assertRedirect();

        $prospect = Prospect::firstOrFail();
        $this->actingAs($user)->put(route('admin.prospects.update', $prospect), [
            'name' => 'Taller Norte', 'phone' => '3312345678', 'source' => 'whatsapp', 'stage' => 'contacted', 'request_type' => 'support',
            'service_interest' => 'alarmas',
        ])->assertRedirect(route('admin.prospects.show', $prospect));

        $this->actingAs($user)->post(route('admin.prospects.activities.store', $prospect), [
            'type' => 'message', 'summary' => 'Se envió información inicial.',
            'happened_at' => '2026-09-24 10:00:00', 'next_action' => 'Confirmar interés',
            'next_action_at' => '2026-09-25 12:00:00',
        ])->assertRedirect();

        $prospect->refresh();
        $this->assertSame('contacted', $prospect->stage);
        $this->assertSame('2026-09-25 12:00:00', $prospect->next_follow_up_at->format('Y-m-d H:i:s'));
        $this->assertDatabaseHas('prospect_activities', ['prospect_id' => $prospect->id, 'type' => 'stage_change', 'old_stage' => 'new', 'new_stage' => 'contacted']);
        $this->assertDatabaseHas('prospect_activities', ['prospect_id' => $prospect->id, 'type' => 'message']);
        $this->actingAs($user)->get(route('admin.prospects.show', $prospect))->assertOk()->assertSee('Taller Norte');
        $this->actingAs($user)->get(route('admin.prospects.index'))->assertOk()->assertSee('Resultados por fuente');
        $this->actingAs($user)->get(route('admin.prospects.today'))->assertOk()->assertSee('Seguimientos vencidos o de hoy');
    }

    public function test_quote_can_be_linked_without_affecting_existing_quotes(): void
    {
        $user = User::factory()->create();
        $prospect = Prospect::create(['name' => 'Condominio Sur', 'phone' => '3311111111', 'source' => 'referral', 'stage' => 'visit']);
        $payload = [
            'prospect_id' => $prospect->id, 'client_name' => 'Condominio Sur', 'quote_date' => '2026-09-24',
            'validity_days' => 15, 'title' => 'Control de acceso', 'status' => 'draft', 'installation_amount' => 0,
            'equipment_warranty_duration' => 1, 'equipment_warranty_unit' => 'years',
            'installation_warranty_duration' => 1, 'installation_warranty_unit' => 'years',
            'items' => [['concept' => 'Equipo', 'quantity' => 1, 'unit_price' => 100]],
        ];

        $this->actingAs($user)->post('/admin/presupuestos', $payload)->assertRedirect();
        $quote = Quote::firstOrFail();
        $this->assertSame($prospect->id, $quote->prospect_id);
        $this->assertSame('quote', $prospect->fresh()->stage);
        $this->assertDatabaseHas('prospect_activities', ['prospect_id' => $prospect->id, 'new_stage' => 'quote']);
    }

    public function test_whatsapp_click_is_stored_as_event_not_prospect(): void
    {
        $this->post('/eventos/whatsapp', ['page' => '/servicios/alarmas', 'service' => 'alarmas'])->assertNoContent();

        $this->assertSame(1, WhatsappClick::count());
        $this->assertSame(0, Prospect::count());
    }

    public function test_expired_unconverted_prospects_are_deleted_after_three_months(): void
    {
        $expired = Prospect::create(['name' => 'Antiguo', 'phone' => '3311111111', 'source' => 'web', 'stage' => 'lost']);
        $expired->forceFill(['created_at' => now()->subMonths(4), 'updated_at' => now()->subMonths(4)])->saveQuietly();
        $customer = Prospect::create(['name' => 'Cliente', 'phone' => '3322222222', 'source' => 'web', 'stage' => 'won']);
        $customer->forceFill(['created_at' => now()->subMonths(4), 'updated_at' => now()->subMonths(4)])->saveQuietly();
        Prospect::create(['name' => 'Reciente', 'phone' => '3333333333', 'source' => 'web', 'stage' => 'new']);

        $this->artisan('prospects:prune')->assertSuccessful();

        $this->assertModelMissing($expired);
        $this->assertModelExists($customer);
        $this->assertDatabaseHas('prospects', ['name' => 'Reciente']);
    }
}

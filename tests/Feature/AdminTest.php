<?php

namespace Tests\Feature;

use App\Models\Kit;
use App\Models\User;
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
}

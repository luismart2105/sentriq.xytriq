<?php

namespace Tests\Feature;

use App\Models\Kit;
use App\Models\User;
use Database\Seeders\IntroductionKitsSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\DatabaseTestCase;

class KitCatalogTest extends DatabaseTestCase
{
    public function test_introduction_packages_are_published_with_images_hdd_and_prices(): void
    {
        $this->seed(IntroductionKitsSeeder::class);
        $this->assertSame(3, Kit::count());
        $page = $this->get('/servicios/camaras-de-seguridad')->assertOk();
        foreach ([4 => '6500.00', 6 => '8000.00', 8 => '9500.00'] as $count => $price) {
            $kit = Kit::where('camera_count', $count)->firstOrFail();
            $this->assertSame($price, $kit->price);
            $this->assertContains('Disco duro de 1 TB incluido e instalado', $kit->features);
            $this->assertFileExists(public_path($kit->image_path));
            $this->assertFileExists(public_path($kit->cabinet_image_path));
            $page->assertSee($kit->name)->assertSee($kit->imageUrl(), false)->assertSee($kit->imageUrl('cabinet_image_path'), false);
        }
        $page->assertSee('Precio de introducción')->assertSee('Alcance y condiciones')
            ->assertSee('cámaras HiLook con visión nocturna')->assertDontSee('100 km')->assertDontSee('LP-WBX-80');
    }

    public function test_import_is_idempotent_and_preserves_admin_changes(): void
    {
        $this->seed(IntroductionKitsSeeder::class);
        $kit = Kit::firstOrFail();
        $kit->update(['price' => 7000, 'active' => false, 'conditions' => 'Alcance editado']);
        $this->seed(IntroductionKitsSeeder::class);
        $this->assertSame(3, Kit::count());
        $this->assertSame('7000.00', $kit->fresh()->price);
        $this->get('/servicios/camaras-de-seguridad')->assertDontSee($kit->name);
        $this->assertSame('Alcance editado', $kit->fresh()->conditions);
    }

    public function test_admin_can_replace_preserve_and_remove_photos_and_edit_offer(): void
    {
        Storage::fake('public');
        $this->seed(IntroductionKitsSeeder::class);
        $kit = Kit::firstOrFail();
        $this->actingAs(User::factory()->create());
        $this->get(route('admin.kits.edit', $kit))->assertOk()
            ->assertSee('multipart/form-data', false)
            ->assertSee('cabinet_image', false)
            ->assertSee('Alcance y condiciones');
        $payload = [
            'name' => $kit->name, 'price' => 6750, 'active' => 1,
            'price_label' => 'Nueva promoción', 'conditions' => 'Nuevo alcance',
            'image_caption' => 'Cámara real', 'cabinet_image_caption' => 'Gabinete real',
        ];
        $photo = fn () => new UploadedFile(public_path('assets/images/kits/hilook-thc-b120-ps-official.png'), 'camera.png', 'image/png', null, true);
        $this->put(route('admin.kits.update', $kit), $payload + ['image' => $photo(), 'cabinet_image' => $photo()])
            ->assertSessionHasNoErrors()->assertRedirect(route('admin.kits.index'));
        $kit->refresh();
        $storedPath = $kit->image_path;
        Storage::disk('public')->assertExists(substr($storedPath, strlen('storage/')));
        Storage::disk('public')->assertExists(substr($kit->cabinet_image_path, strlen('storage/')));
        $this->assertSame('6750.00', $kit->price);
        $this->get('/servicios/camaras-de-seguridad')->assertSee('Nueva promoción')->assertSee('Nuevo alcance')->assertSee($kit->imageUrl(), false);
        $this->put(route('admin.kits.update', $kit), $payload)->assertSessionHasNoErrors();
        $this->assertSame($storedPath, $kit->fresh()->image_path);
        $this->put(route('admin.kits.update', $kit), $payload + ['remove_image' => 1, 'remove_cabinet_image' => 1])->assertSessionHasNoErrors();
        $this->assertNull($kit->fresh()->image_path);
        $this->assertNull($kit->fresh()->cabinet_image_path);
    }

    public function test_upload_requires_authentication_and_rejects_non_images(): void
    {
        $this->post('/admin/kits', ['name' => 'Forbidden'])->assertRedirect('/admin/ingresar');
        $this->actingAs(User::factory()->create())->post('/admin/kits', [
            'name' => 'Invalid image', 'price' => 100,
            'image' => UploadedFile::fake()->create('payload.php', 1, 'application/x-php'),
        ])->assertSessionHasErrors('image');
        $this->assertSame(0, Kit::count());
    }
}

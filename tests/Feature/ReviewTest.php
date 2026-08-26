<?php

namespace Tests\Feature;

use App\Models\Review;
use Illuminate\Support\Str;
use Tests\DatabaseTestCase;

class ReviewTest extends DatabaseTestCase
{
    public function test_private_review_link_accepts_one_submission_and_requires_approval(): void
    {
        $review = Review::create([
            'token' => (string) Str::uuid(),
            'status' => 'invited',
        ]);

        $this->get(route('reviews.show', $review->token))->assertOk();

        $this->post(route('reviews.store', $review->token), [
            'client_name' => 'María López',
            'municipality' => 'Zapopan',
            'service' => 'Cámaras de seguridad',
            'rating' => 5,
            'comment' => 'La instalación fue ordenada y nos explicaron claramente cómo usar el sistema.',
        ])->assertSessionHas('review_message');

        $review->refresh();
        $this->assertSame('pending', $review->status);
        $this->get('/')->assertDontSee('La instalación fue ordenada');

        $review->update(['status' => 'approved', 'approved_at' => now()]);

        $this->get('/')
            ->assertSee('La instalación fue ordenada')
            ->assertSee('María')
            ->assertDontSee('María López');
    }
}

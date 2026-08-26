<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewSubmissionController extends Controller
{
    public function show(string $token): View
    {
        $review = Review::where('token', $token)->firstOrFail();

        return view('reviews.submit', compact('review'));
    }

    public function store(Request $request, string $token): RedirectResponse
    {
        $review = Review::where('token', $token)->firstOrFail();

        if ($review->status !== 'invited') {
            return back()->with('review_message', 'Esta invitación ya fue utilizada. Gracias por compartir tu experiencia.');
        }

        $validated = $request->validate([
            'client_name' => ['required', 'string', 'max:100'],
            'municipality' => ['required', 'string', 'max:100'],
            'service' => ['required', 'string', 'max:150'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['required', 'string', 'min:20', 'max:1500'],
            'photo' => ['nullable', 'image', 'max:4096'],
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo_path'] = $request->file('photo')->store('reviews', 'public');
        }

        unset($validated['photo']);

        $review->update([
            ...$validated,
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        return back()->with('review_message', '¡Gracias! Tu reseña fue enviada y será revisada antes de publicarse.');
    }
}

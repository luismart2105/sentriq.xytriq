<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(): View
    {
        return view('admin.reviews.index', [
            'reviews' => Review::latest()->get(),
        ]);
    }

    public function invite(): RedirectResponse
    {
        $review = Review::create([
            'token' => (string) Str::uuid(),
            'status' => 'invited',
        ]);

        return redirect()->route('admin.reviews.index')->with([
            'status' => 'Enlace privado creado.',
            'invite_url' => route('reviews.show', $review->token),
        ]);
    }

    public function status(Request $request, Review $review): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:approved,rejected,pending'],
        ]);

        $review->update([
            'status' => $validated['status'],
            'approved_at' => $validated['status'] === 'approved' ? now() : null,
        ]);

        return back()->with('status', 'Estado de la reseña actualizado.');
    }
}

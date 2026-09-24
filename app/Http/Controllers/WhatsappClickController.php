<?php

namespace App\Http\Controllers;

use App\Models\WhatsappClick;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class WhatsappClickController extends Controller
{
    public function store(Request $request): Response
    {
        $data = $request->validate([
            'page' => ['required', 'string', 'max:120'],
            'service' => ['nullable', Rule::in(array_keys(config('sentriq.services')))],
            'utm_source' => ['nullable', 'string', 'max:120'],
            'utm_medium' => ['nullable', 'string', 'max:120'],
            'utm_campaign' => ['nullable', 'string', 'max:160'],
        ]);

        WhatsappClick::create([...$data, 'clicked_at' => now()]);

        return response()->noContent();
    }
}

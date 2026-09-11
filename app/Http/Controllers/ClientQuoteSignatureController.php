<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ClientQuoteSignatureController extends Controller
{
    public function show(string $token): View
    {
        return view('quotes.sign', ['quote' => $this->quote($token)]);
    }

    public function document(string $token): View
    {
        return view('admin.quotes.show', [
            'quote' => $this->quote($token),
            'publicMode' => true,
        ]);
    }

    public function store(Request $request, string $token): RedirectResponse
    {
        $quote = $this->quote($token);

        if ($quote->signed_at) {
            return back()->with('status', 'Este presupuesto ya fue firmado.');
        }

        $validated = $request->validate([
            'client_signer_name' => ['required', 'string', 'max:160'],
            'signature_data' => ['required', 'string', 'max:3000000'],
            'acceptance' => ['accepted'],
        ]);

        $image = $this->decodeSignature($validated['signature_data']);
        $path = 'signatures/clients/'.Str::uuid().'.png';
        abort_unless(Storage::disk('public')->put($path, $image), 500, 'No se pudo guardar la firma.');

        $quote->update([
            'client_signer_name' => $validated['client_signer_name'],
            'client_signature_path' => 'storage/'.$path,
            'signed_at' => now(),
            'signed_ip' => $request->ip(),
            'signed_user_agent' => Str::limit((string) $request->userAgent(), 1000, ''),
            'status' => 'accepted',
        ]);

        return back()->with('status', 'Firma registrada correctamente. Gracias por autorizar el presupuesto.');
    }

    private function quote(string $token): Quote
    {
        return Quote::where('signing_token', $token)->firstOrFail();
    }

    private function decodeSignature(string $dataUrl): string
    {
        if (! preg_match('/^data:image\/png;base64,([A-Za-z0-9+\/=]+)$/', $dataUrl, $matches)) {
            throw ValidationException::withMessages(['signature_data' => 'La firma recibida no es válida.']);
        }

        $image = base64_decode($matches[1], true);
        $dimensions = $image === false ? false : @getimagesizefromstring($image);
        if ($image === false || strlen($image) > 2 * 1024 * 1024 || $dimensions === false || $dimensions['mime'] !== 'image/png') {
            throw ValidationException::withMessages(['signature_data' => 'No se pudo procesar la firma. Intenta nuevamente.']);
        }

        return $image;
    }
}

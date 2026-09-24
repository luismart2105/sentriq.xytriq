<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Prospect;
use App\Models\Quote;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class QuoteController extends Controller
{
    public function index(): View
    {
        return view('admin.quotes.index', ['quotes' => Quote::latest('quote_date')->latest()->get()]);
    }

    public function create(Request $request): View
    {
        return view('admin.quotes.create', [
            'quote' => new Quote([
                'prospect_id' => $request->integer('prospect_id') ?: null,
                'number' => $this->nextNumber(now()),
                'quote_date' => now(), 'validity_days' => 15,
                'title' => 'Presupuesto de solución de seguridad inteligente',
                'equipment_warranty_duration' => 1,
                'equipment_warranty_unit' => 'years',
                'equipment_warranty' => 'Los equipos incluidos cuentan con 1 año de garantía contra defectos de fabricación, contado a partir de la entrega y sujeto a revisión del componente. No cubre daños por golpes, humedad derivada de modificaciones, vandalismo, variaciones eléctricas, uso inadecuado o intervención de terceros.',
                'installation_warranty_duration' => 2,
                'installation_warranty_unit' => 'years',
                'installation_warranty' => 'La mano de obra de instalación cuenta con 2 años de garantía. Cubre correcciones necesarias por fallas directamente atribuibles al montaje, fijaciones, terminaciones, cableado instalado o configuración realizada por Sentriq. No cubre cambios posteriores en red, Internet, energía, portón, obra civil ni modificaciones hechas por terceros.',
                'status' => 'draft',
            ]),
            'prospects' => Prospect::whereNotIn('stage', ['lost', 'unqualified'])->latest()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $data = $this->storeSignature($request, $data);
        $quote = $this->createWithAutomaticNumber($data);
        $this->recordQuoteStage($request, $quote);

        return redirect()->route('admin.quotes.edit', $quote)->with('status', 'Presupuesto creado correctamente.');
    }

    public function show(Quote $quote): View
    {
        return view('admin.quotes.show', compact('quote'));
    }

    public function edit(Quote $quote): View
    {
        return view('admin.quotes.edit', [
            'quote' => $quote,
            'prospects' => Prospect::whereNotIn('stage', ['lost', 'unqualified'])->latest()->get(),
        ]);
    }

    public function update(Request $request, Quote $quote): RedirectResponse
    {
        if ($quote->signed_at) {
            return back()->with('status', 'El presupuesto firmado no puede modificarse. Duplícalo para crear una nueva versión.');
        }

        $data = $this->validatedData($request, $quote);
        $data = $this->storeSignature($request, $data, $quote);
        $quote->update($data);
        $this->recordQuoteStage($request, $quote);

        return redirect()->route('admin.quotes.edit', $quote)->with('status', 'Presupuesto actualizado correctamente.');
    }

    public function destroy(Quote $quote): RedirectResponse
    {
        $this->deleteSignature($quote->signature_path);
        $this->deleteSignature($quote->client_signature_path);
        $quote->delete();

        return redirect()->route('admin.quotes.index')->with('status', 'Presupuesto eliminado.');
    }

    public function duplicate(Quote $quote): RedirectResponse
    {
        $data = $quote->replicate(['number'])->toArray();
        $data['quote_date'] = now();
        $data['status'] = 'draft';
        unset($data['signature_path'], $data['signing_token'], $data['client_signer_name'], $data['client_signature_path'], $data['signed_at'], $data['signed_ip'], $data['signed_user_agent']);
        if ($quote->signature_path && Storage::disk('public')->exists(Str::after($quote->signature_path, 'storage/'))) {
            $source = Str::after($quote->signature_path, 'storage/');
            $copyPath = 'signatures/'.Str::uuid().'.png';
            Storage::disk('public')->copy($source, $copyPath);
            $data['signature_path'] = 'storage/'.$copyPath;
        }
        $copy = $this->createWithAutomaticNumber($data);

        return redirect()->route('admin.quotes.edit', $copy)->with('status', 'Copia creada; ya puedes personalizarla.');
    }

    public function signingLink(Quote $quote): RedirectResponse
    {
        $this->deleteSignature($quote->client_signature_path);
        $quote->update([
            'signing_token' => Str::random(48),
            'client_signer_name' => null,
            'client_signature_path' => null,
            'signed_at' => null,
            'signed_ip' => null,
            'signed_user_agent' => null,
            'status' => 'sent',
        ]);

        return back()->with('status', 'Enlace privado generado correctamente.');
    }

    private function validatedData(Request $request, ?Quote $quote = null): array
    {
        $validated = $request->validate([
            'prospect_id' => ['nullable', 'exists:prospects,id'],
            'client_name' => ['required', 'string', 'max:160'],
            'quote_date' => ['required', 'date'],
            'validity_days' => ['required', 'integer', 'min:1', 'max:365'],
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:1000'],
            'installation_amount' => ['nullable', 'numeric', 'min:0', 'max:999999999'],
            'deposit_amount' => ['nullable', 'numeric', 'min:0', 'max:999999999'],
            'equipment_warranty_duration' => ['required', 'integer', 'min:0', 'max:1200'],
            'equipment_warranty_unit' => ['required', Rule::in(['months', 'years'])],
            'equipment_warranty' => ['nullable', 'required_if:equipment_warranty_duration,0', 'string', 'max:4000'],
            'installation_warranty_duration' => ['required', 'integer', 'min:0', 'max:1200'],
            'installation_warranty_unit' => ['required', Rule::in(['months', 'years'])],
            'installation_warranty' => ['nullable', 'required_if:installation_warranty_duration,0', 'string', 'max:4000'],
            'installation_scope' => ['nullable', 'string', 'max:8000'],
            'project_considerations' => ['nullable', 'string', 'max:8000'],
            'project_manager' => ['nullable', 'string', 'max:160'],
            'signature_data' => ['nullable', 'string', 'max:3000000'],
            'remove_signature' => ['nullable', 'boolean'],
            'status' => ['required', Rule::in(['draft', 'sent', 'accepted'])],
            'items' => ['required', 'array', 'min:1', 'max:100'],
            'items.*.concept' => ['required', 'string', 'max:255'],
            'items.*.model' => ['nullable', 'string', 'max:255'],
            'items.*.benefit' => ['nullable', 'string', 'max:1000'],
            'items.*.quantity' => ['required', 'numeric', 'gt:0', 'max:99999'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0', 'max:999999999'],
        ]);

        $validated['items'] = collect($validated['items'])->map(fn (array $item): array => [
            'concept' => trim($item['concept']),
            'model' => trim($item['model'] ?? ''),
            'benefit' => trim($item['benefit'] ?? ''),
            'quantity' => (float) $item['quantity'],
            'unit_price' => round((float) $item['unit_price'], 2),
        ])->values()->all();
        $validated['installation_amount'] = $validated['installation_amount'] ?? 0;
        $validated['deposit_amount'] = $validated['deposit_amount'] ?? 0;

        $total = collect($validated['items'])->sum(fn (array $item): float => $item['quantity'] * $item['unit_price'])
            + (float) $validated['installation_amount'];
        if ((float) $validated['deposit_amount'] > $total) {
            throw ValidationException::withMessages([
                'deposit_amount' => 'El anticipo no puede ser mayor al total del proyecto.',
            ]);
        }

        return $validated;
    }

    private function storeSignature(Request $request, array $data, ?Quote $quote = null): array
    {
        unset($data['signature_data'], $data['remove_signature']);

        if ($request->filled('signature_data')) {
            $image = $this->decodeSignature($request->string('signature_data')->toString());
            $path = 'signatures/'.Str::uuid().'.png';
            abort_unless(Storage::disk('public')->put($path, $image), 500, 'No se pudo guardar la firma.');
            $this->deleteSignature($quote?->signature_path);
            $data['signature_path'] = 'storage/'.$path;
        } elseif ($request->boolean('remove_signature')) {
            $this->deleteSignature($quote?->signature_path);
            $data['signature_path'] = null;
        }

        return $data;
    }

    private function decodeSignature(string $dataUrl): string
    {
        if (! preg_match('/^data:image\/png;base64,([A-Za-z0-9+\/=]+)$/', $dataUrl, $matches)) {
            throw ValidationException::withMessages(['signature_data' => 'La firma recibida no es válida.']);
        }

        $image = base64_decode($matches[1], true);
        $dimensions = $image === false ? false : @getimagesizefromstring($image);

        if ($image === false || strlen($image) > 2 * 1024 * 1024 || $dimensions === false || $dimensions['mime'] !== 'image/png') {
            throw ValidationException::withMessages(['signature_data' => 'No se pudo procesar la firma. Intenta dibujarla nuevamente.']);
        }

        return $image;
    }

    private function deleteSignature(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete(Str::after($path, 'storage/'));
        }
    }

    private function createWithAutomaticNumber(array $data): Quote
    {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            try {
                return Quote::create([
                    ...$data,
                    'number' => $this->nextNumber(Carbon::parse($data['quote_date'])),
                ]);
            } catch (QueryException $exception) {
                if (! $this->isDuplicateNumber($exception) || $attempt === 4) {
                    throw $exception;
                }
            }
        }

        throw new \RuntimeException('No se pudo generar un folio disponible.');
    }

    private function nextNumber(Carbon $date): string
    {
        $prefix = 'COT-'.$date->format('Y-md').'-';
        $lastSequence = Quote::query()
            ->where('number', 'like', $prefix.'%')
            ->pluck('number')
            ->map(fn (string $number): int => (int) substr($number, strlen($prefix)))
            ->max() ?? 0;

        return $prefix.str_pad((string) ($lastSequence + 1), 2, '0', STR_PAD_LEFT);
    }

    private function isDuplicateNumber(QueryException $exception): bool
    {
        return in_array((string) $exception->getCode(), ['23000', '23505'], true);
    }

    private function recordQuoteStage(Request $request, Quote $quote): void
    {
        $prospect = $quote->prospect;
        if (! $prospect || in_array($prospect->stage, ['quote', 'won', 'lost', 'unqualified'], true)) {
            return;
        }

        $oldStage = $prospect->stage;
        $prospect->update(['stage' => 'quote']);
        $prospect->activities()->create([
            'user_id' => $request->user()->id,
            'type' => 'stage_change',
            'summary' => 'Etapa actualizada al crear el presupuesto '.$quote->number.'.',
            'old_stage' => $oldStage,
            'new_stage' => 'quote',
            'happened_at' => now(),
        ]);
    }
}

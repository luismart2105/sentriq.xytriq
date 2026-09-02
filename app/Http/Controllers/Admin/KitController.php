<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class KitController extends Controller
{
    public function index(): View
    {
        return view('admin.kits.index', [
            'kits' => Kit::orderBy('sort_order')->orderBy('price')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.kits.create', ['kit' => new Kit]);
    }

    public function store(Request $request): RedirectResponse
    {
        Kit::create($this->validatedData($request));

        return redirect()->route('admin.kits.index')->with('status', 'Kit creado correctamente.');
    }

    public function edit(Kit $kit): View
    {
        return view('admin.kits.edit', compact('kit'));
    }

    public function update(Request $request, Kit $kit): RedirectResponse
    {
        $kit->update($this->validatedData($request, $kit));

        return redirect()->route('admin.kits.index')->with('status', 'Kit actualizado correctamente.');
    }

    public function destroy(Kit $kit): RedirectResponse
    {
        $kit->delete();

        return redirect()->route('admin.kits.index')->with('status', 'Kit eliminado.');
    }

    private function validatedData(Request $request, ?Kit $kit = null): array
    {
        $request->merge([
            'slug' => Str::slug($request->input('slug') ?: $request->input('name', '')),
        ]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'slug' => [
                'required',
                'string',
                'max:140',
                Rule::unique('kits', 'slug')->ignore($kit?->id),
            ],
            'camera_count' => ['nullable', 'integer', 'min:1', 'max:64'],
            'price' => ['required', 'numeric', 'min:0', 'max:99999999'],
            'description' => ['nullable', 'string', 'max:1000'],
            'price_label' => ['nullable', 'string', 'max:80'],
            'conditions' => ['nullable', 'string', 'max:4000'],
            'image_caption' => ['nullable', 'string', 'max:255'],
            'cabinet_image_caption' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'cabinet_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'remove_image' => ['nullable', 'boolean'],
            'remove_cabinet_image' => ['nullable', 'boolean'],
            'features_text' => ['nullable', 'string', 'max:4000'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
        ]);

        $features = collect(preg_split('/\r\n|\r|\n/', $validated['features_text'] ?? ''))
            ->map(fn (string $feature) => trim($feature))
            ->filter()
            ->values()
            ->all();

        $data = [
            'service_slug' => 'camaras-de-seguridad',
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'camera_count' => $validated['camera_count'] ?? null,
            'price' => $validated['price'],
            'description' => $validated['description'] ?? null,
            'price_label' => $validated['price_label'] ?? null,
            'conditions' => $validated['conditions'] ?? null,
            'image_caption' => $validated['image_caption'] ?? null,
            'cabinet_image_caption' => $validated['cabinet_image_caption'] ?? null,
            'features' => $features,
            'installation_included' => $request->boolean('installation_included'),
            'featured' => $request->boolean('featured'),
            'active' => $request->boolean('active'),
            'sort_order' => $validated['sort_order'] ?? 0,
        ];

        foreach (['image', 'cabinet_image'] as $field) {
            if ($request->hasFile($field)) {
                $path = $request->file($field)->store('kits', 'public');
                abort_unless($path, 500, 'No se pudo guardar la imagen.');
                $data[$field.'_path'] = 'storage/'.$path;
            } elseif ($request->boolean('remove_'.$field)) {
                $data[$field.'_path'] = null;
            }
        }

        return $data;
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Kit;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(): View
    {
        return view('services.index', [
            'services' => config('sentriq.services'),
        ]);
    }

    public function show(string $service): View
    {
        $services = config('sentriq.services');

        abort_unless(isset($services[$service]), 404);

        return view('services.show', [
            'slug' => $service,
            'service' => $services[$service],
            'services' => $services,
            'kits' => $service === 'camaras-de-seguridad'
                ? Kit::published()->get()
                : collect(),
        ]);
    }
}

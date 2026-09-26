<?php

namespace App\Http\Controllers;

use App\Models\Prospect;
use App\Models\User;
use App\Notifications\NewProspectNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class ContactController extends Controller
{
    public function create(Request $request): View
    {
        return view('contact', ['campaign' => $this->campaignData($request)]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(config('sentriq.leads.form_enabled'), 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'request_type' => ['required', Rule::in(array_keys(Prospect::REQUEST_TYPES))],
            'phone' => ['nullable', 'required_without:email', 'string', 'max:40'],
            'email' => ['nullable', 'required_without:phone', 'email:rfc', 'max:255'],
            'service_interest' => ['required', Rule::in(array_keys(config('sentriq.services')))],
            'municipality' => ['required', 'string', 'max:120'],
            'description' => ['required', 'string', 'max:2000'],
            'privacy_accepted' => ['accepted'],
            'website' => ['nullable', 'max:0'],
            'utm_source' => ['nullable', 'string', 'max:120'],
            'utm_medium' => ['nullable', 'string', 'max:120'],
            'utm_campaign' => ['nullable', 'string', 'max:160'],
            'utm_term' => ['nullable', 'string', 'max:160'],
            'utm_content' => ['nullable', 'string', 'max:160'],
            'landing_page' => ['nullable', 'string', 'max:500'],
            'referrer' => ['nullable', 'string', 'max:500'],
        ]);

        $prospect = DB::transaction(function () use ($data): Prospect {
            $data['referrer'] = $this->safeUrlWithoutQuery($data['referrer'] ?? null);
            $data['landing_page'] = $this->safeUrlWithoutQuery($data['landing_page'] ?? null);
            $prospect = Prospect::create([
                ...collect($data)->except(['privacy_accepted', 'website'])->all(),
                'source' => 'web',
                'campaign' => $data['utm_campaign'] ?? null,
                'stage' => 'new',
                'assigned_user_id' => User::where('email', config('sentriq.leads.default_assignee_email'))->value('id'),
            ]);
            $prospect->activities()->create([
                'type' => 'note',
                'summary' => 'Formulario de contacto recibido desde el sitio web.',
                'happened_at' => now(),
            ]);

            return $prospect;
        });

        try {
            Notification::route('mail', config('sentriq.leads.notification_email'))
                ->notify(new NewProspectNotification($prospect));
        } catch (Throwable $exception) {
            Log::warning('No se pudo notificar el nuevo prospecto.', [
                'prospect_id' => $prospect->id,
                'exception' => $exception->getMessage(),
            ]);
        }

        return redirect()->route('contact')->with('contact_success', 'Recibimos tus datos. Te responderemos dentro de 24 horas hábiles.');
    }

    private function campaignData(Request $request): array
    {
        return collect(['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'])
            ->mapWithKeys(fn (string $key): array => [
                $key => mb_substr($request->string($key, $request->session()->get('sentriq_campaign.'.$key, ''))->toString(), 0, 160),
            ])
            ->all();
    }

    private function safeUrlWithoutQuery(?string $url): ?string
    {
        if (! $url || ! filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        $parts = parse_url($url);
        if (! isset($parts['scheme'], $parts['host']) || ! in_array(strtolower($parts['scheme']), ['http', 'https'], true)) {
            return null;
        }

        return mb_substr(strtolower($parts['scheme']).'://'.$parts['host'].($parts['path'] ?? ''), 0, 500);
    }
}

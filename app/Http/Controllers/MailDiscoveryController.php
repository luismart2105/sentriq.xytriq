<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MailDiscoveryController extends Controller
{
    public function autoconfig(): Response
    {
        return response()->view('mail.autoconfig', [
            'mailDomain' => config('sentriq.mail.domain'),
            'mailHost' => config('sentriq.mail.host'),
        ], 200, $this->xmlHeaders(cacheable: true));
    }

    public function autodiscover(Request $request): Response
    {
        $email = $this->emailFromRequest($request);

        if (! $email || ! str_ends_with(strtolower($email), '@'.config('sentriq.mail.domain'))) {
            return response()->view('mail.autodiscover-error', status: 422, headers: $this->xmlHeaders());
        }

        return response()->view('mail.autodiscover', [
            'email' => $email,
            'mailHost' => config('sentriq.mail.host'),
        ], 200, $this->xmlHeaders());
    }

    private function emailFromRequest(Request $request): ?string
    {
        $email = $request->string('emailaddress')->trim()->toString();

        if (! $email && $request->getContent()) {
            preg_match('/<EMailAddress>\s*([^<]+)\s*<\/EMailAddress>/i', $request->getContent(), $matches);
            $email = html_entity_decode(trim($matches[1] ?? ''), ENT_QUOTES | ENT_XML1, 'UTF-8');
        }

        return filter_var($email, FILTER_VALIDATE_EMAIL) ? mb_strtolower($email) : null;
    }

    private function xmlHeaders(bool $cacheable = false): array
    {
        return [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Cache-Control' => $cacheable
                ? 'public, max-age=3600'
                : 'no-store, no-cache, must-revalidate, max-age=0',
            'X-Content-Type-Options' => 'nosniff',
        ];
    }
}

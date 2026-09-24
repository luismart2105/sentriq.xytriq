<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CaptureCampaign
{
    public function handle(Request $request, Closure $next): Response
    {
        foreach (['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'] as $key) {
            if ($request->query->has($key)) {
                $request->session()->put('sentriq_campaign.'.$key, mb_substr($request->string($key)->toString(), 0, 160));
            }
        }

        return $next($request);
    }
}

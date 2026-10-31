<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\Http;
use App\Jobs\SendVisitorEmail;

class TrackVisitor
{
    public function handle(Request $request, Closure $next)
    {
        // 1. Only track actual page views (not assets, AJAX, or API calls)
        if ($request->is('api/*') || $request->ajax() || $request->wantsJson()) {
            return $next($request);
        }

        // 2. Skip if the visitor cookie already exists
        if ($request->cookie('visitor_tracked')) {
            return $next($request);
        }

        // 3. Extract request values safely without serializing the full Request object
        $ip       = $request->ip();
        $userAgent = $request->header('user-agent');
        $referrer = $request->headers->get('referer', 'Direct');
        $screen   = ($request->header('sec-ch-width') && $request->header('sec-ch-height'))
            ? $request->header('sec-ch-width') . 'x' . $request->header('sec-ch-height')
            : 'Unknown';

        dispatch(function () use ($ip, $userAgent, $referrer, $screen) {
            $agent = new Agent();
            if ($userAgent) {
                $agent->setUserAgent($userAgent);
            }

            $device  = $agent->device() ?: 'Unknown';
            $browser = $agent->browser() . ' ' . $agent->version($agent->browser());
            $os      = $agent->platform() . ' ' . $agent->version($agent->platform());

            $location = ['country' => 'Unknown', 'city' => 'Unknown', 'region' => 'Unknown'];

            try {
                $response = Http::timeout(5)->get("http://ip-api.com/json/{$ip}?fields=country,city,regionName");
                if ($response->successful()) {
                    $data = $response->json();
                    $location['country'] = $data['country'] ?? 'Unknown';
                    $location['city']    = $data['city'] ?? 'Unknown';
                    $location['region']  = $data['regionName'] ?? 'Unknown';
                }
            } catch (\Throwable $e) {
                // fail silently
            }

            $data = [
                'datetime' => now()->toDateTimeString(),
                'device'   => $device,
                'browser'  => $browser,
                'os'       => $os,
                'ip'       => $ip,
                'country'  => $location['country'],
                'city'     => $location['city'],
                'region'   => $location['region'],
                'referrer' => $referrer,
                'screen'   => $screen,
            ];

            try {
                SendVisitorEmail::dispatch($data);
            } catch (\Throwable $e) {
                // fail silently
            }
        })->afterResponse();

        // 4. Set a cookie to prevent duplicate tracking for 30 days
        $response = $next($request);
        return $response->withCookie(cookie()->forever('visitor_tracked', 1));
    }
}

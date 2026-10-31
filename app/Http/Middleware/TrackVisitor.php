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

        // 3. Dispatch the tracking job (runs after response)
        dispatch(function () use ($request) {
            $agent = new Agent();

            $device  = $agent->device() ?: 'Unknown';
            $browser = $agent->browser() . ' ' . $agent->version($agent->browser());
            $os      = $agent->platform() . ' ' . $agent->version($agent->platform());

            $ip       = $request->ip();
            $location = ['country' => 'Unknown', 'city' => 'Unknown', 'region' => 'Unknown'];

            try {
                $response = Http::timeout(5)->get("http://ip-api.com/json/{$ip}?fields=country,city,regionName");
                if ($response->successful()) {
                    $data = $response->json();
                    $location['country'] = $data['country'] ?? 'Unknown';
                    $location['city']    = $data['city'] ?? 'Unknown';
                    $location['region']  = $data['regionName'] ?? 'Unknown';
                }
            } catch (\Exception $e) {
                // fail silently
            }

            $referrer = $request->headers->get('referer', 'Direct');
            $screen   = $request->header('sec-ch-width') . 'x' . $request->header('sec-ch-height') ?: 'Unknown';

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

            SendVisitorEmail::dispatch($data);
        })->afterResponse();

        // 4. Set a cookie to prevent duplicate tracking for 30 days
        $response = $next($request);
        return $response->withCookie(cookie()->forever('visitor_tracked', 1));
    }
}
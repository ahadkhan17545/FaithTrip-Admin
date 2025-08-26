<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CheckUserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        $ua   = $request->userAgent() ?? '';

        // --- very small UA helpers (no package required) ---
        $browser = 'Unknown';
        if (stripos($ua, 'Edg/') !== false)      $browser = 'Edge';
        elseif (stripos($ua, 'OPR/') !== false)  $browser = 'Opera';
        elseif (stripos($ua, 'Chrome/') !== false) $browser = 'Chrome';
        elseif (stripos($ua, 'Firefox/') !== false) $browser = 'Firefox';
        elseif (stripos($ua, 'Safari/') !== false)  $browser = 'Safari';

        $os = 'Unknown';
        if (stripos($ua, 'Windows') !== false)        $os = 'Windows';
        elseif (stripos($ua, 'Android') !== false)    $os = 'Android';
        elseif (stripos($ua, 'iPhone') !== false)     $os = 'iOS';
        elseif (stripos($ua, 'iPad') !== false)       $os = 'iPadOS';
        elseif (stripos($ua, 'Mac OS X') !== false
             || stripos($ua, 'Macintosh') !== false)  $os = 'macOS';
        elseif (stripos($ua, 'Linux') !== false)      $os = 'Linux';

        $device = 'Desktop';
        if (stripos($ua, 'Mobile') !== false)         $device = 'Mobile';
        if (stripos($ua, 'Tablet') !== false || stripos($ua, 'iPad') !== false) $device = 'Tablet';


        DB::table('activity_logs')->insert([
            'user_id' => $user->id,
            'route' => $request->path(),
            'device' => $os,
            'browser' => $browser,
            'os' => $device,
            'ip_address' => $request->ip(),
            'created_at' => Carbon::now()
        ]);

        if($user->status == 1){
            return $next($request);
        } else {
            return abort(403);
        }
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Log;
use App\Jobs\ProcessReferer;

class TrapReferer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
	$referer = $request->header('referer');
	$app_url_no_proto  =  str_replace('https://', '', str_replace('http://', '', config('app.url') ) );
	$referer_no_proto  =  str_replace('https://', '', str_replace('http://', '', $referer ) );

	if(!empty($referer) && preg_match('/^'.$app_url_no_proto .'/', $referer_no_proto) == 0){
        //wrap in if vouch

        $pr = new ProcessReferer($referer);
        dispatch($pr);
	    //Log::debug(print_r($referer, true));
	}
        return $next($request);
    }
}

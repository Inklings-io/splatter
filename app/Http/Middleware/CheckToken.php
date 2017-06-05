<?php

namespace App\Http\Middleware;

use Closure;
use App\Token;
use Carbon\Carbon;

class CheckToken
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
        if(empty($request->header('Authorization'))){
            abort(401);
        }
        $tok_string = explode(' ', $request->header('Authorization'))[1];
        $token_parts = explode(',', $tok_string);
        $token_obj = Token::find($token_parts[0]);

        if(empty($token_obj) || $token_obj->checksum != $token_parts[1]){
            abort(401);
        }
        $last_used = strtotime($token_obj->last_used);

        //if older than 30 days
        if($last_used < (time() - 60*60*24*30)){
            abort(401);
        }

        $token_obj->last_used = Carbon::now();
        $token_obj->save();

        $request->attributes->add([
            'scope' => explode(' ', $token_obj->scope),
            'client_id' => $token_obj->client_id,
            'user' => $token_obj->user
        ]);

        return $next($request);
    }
}

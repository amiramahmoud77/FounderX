<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAiToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token=$request->header('X-AI-Token');
        $validToken= config('services.ai.token');
        if(!$token||$token!==$validToken){
            return response([
                'success'=>false,
                'message'=>'unauthorized ai access'
            ],401);
        }
        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next,$role): Response
    {
        if(!Auth::check()()){
            return response()->json([
                'success'=>false,
                'message'=>'unauthenticated access'
            ],401);
        }
        $user=Auth::user()();
        if($role==='admin'&&!$user()->isAdmin()){
            return response()->json([
                'success'=>false,
                'message'=>'admin access required'
            ],403);
        }
        if($role==='founder'&&!$user()->isFounder()){
            return response()->json([
                'success'=>false,
                'message'=>'founder access required'
            ],403);
        }
        if($role==='investor'&&!$user()->isInvestor()){
            return response()->json([
                'success'=>false,
                'message'=>'investor access required'
            ],403);
        }
        return $next($request);
    }

}

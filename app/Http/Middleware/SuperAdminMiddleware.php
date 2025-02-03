<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
   
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::user()->role == 'superadmin') {
            return $next($request);
        }else{
            return back();
        }
    }
}

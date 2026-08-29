<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Model\Admin;
use Illuminate\Support\Facades\Auth;
Use Redirect;
Use Route,DB,App,Config;
Use Session;


class GuestCustomer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {   

        if(Auth::guard('customer')->check()){
            return Redirect()->route('front-user.dashboard');
        }
      
        return $next($request);
    }
}

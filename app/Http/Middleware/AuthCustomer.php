<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Model\Admin;
use Illuminate\Support\Facades\Auth;
Use Redirect;
Use Route,DB,App,Config;
Use Session;


class AuthCustomer
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

        if(!Auth::guard('customer')->check()){
            return Redirect()->route('front-user.login');
        }
            
        if(Auth::guard('customer')->user()->user_role_id != 2){
            Session()->flash('error', trans("Invalid Access"));
            return Redirect()->route('front-user.login');
        }

        if(Auth::guard('customer')->user()->is_deleted == 1 || Auth::guard('customer')->user()->is_active == 0){
            Session()->flash('error', trans("your account has been deactivated please contact to admin for more details"));
            Auth::guard('customer')->logout();
        }
        

		if (Session::has('applocale')) {
            $applocale            = Session::get('applocale');

        }else {
            $applocale            = Config::get('app.fallback_locale');
        }
        App::setLocale($applocale);
        return $next($request);
    }
}

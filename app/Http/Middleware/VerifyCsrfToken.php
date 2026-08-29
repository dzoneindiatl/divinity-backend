<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Closure,Session;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
        'variant-combination/prices',
        'base/uploder',
        'get-varient/*'
    ];


    public function handle($request, Closure $next)
    {
        if ($request->wantsJson()) {
            return $next($request);
        }

        if(!Session::has('currency')){
            Session::put('currency', 'INR');
        }

        return parent::handle($request, $next);
    }
}

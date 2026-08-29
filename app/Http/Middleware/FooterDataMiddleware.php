<?php

namespace App\Http\Middleware;

use App\Models\FooterSubcategory;
use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class FooterDataMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Fetch footer data (example: categories, settings, links)
        $footerData = [
            'settings' => Setting::pluck('value', 'key')->toArray(),
            'footer_subcategories' => FooterSubcategory::all()
        ];

        // Share footer data with all views
        View::share('footerData', $footerData);

        return $next($request);
    }
}

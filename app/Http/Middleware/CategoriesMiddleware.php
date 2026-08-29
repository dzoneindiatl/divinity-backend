<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Category;
use Illuminate\Support\Facades\View;

class CategoriesMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        $categories = new Category;
        // $all_categories = $categories->getActiveCategories();
        $all_categories = $categories->getAllCategories();
        
        // Share data globally
        View::share('all_categories', $all_categories);

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // اللغات المدعومة
        $supportedLocales = ['en', 'ar'];
        
        // محاولة الحصول على اللغة من Session
        $locale = Session::get('locale');
        
        // إذا لم توجد في Session، استخدم اللغة الافتراضية من config
        if (!$locale || !in_array($locale, $supportedLocales)) {
            $locale = config('app.locale', 'en');
        }
        
        // ضبط اللغة الحالية
        App::setLocale($locale);
        
        // تمرير الطلب للخطوة التالية
        return $next($request);
    }
}


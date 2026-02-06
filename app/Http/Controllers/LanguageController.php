<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    /**
     * تبديل اللغة
     *
     * @param string $locale
     * @return \Illuminate\Http\RedirectResponse
     */
    public function switch($locale)
    {
        // اللغات المدعومة
        $supportedLocales = ['en', 'ar'];
        
        // التحقق من صحة اللغة
        if (!in_array($locale, $supportedLocales)) {
            $locale = config('app.locale', 'en');
        }

        // حفظ اللغة في Session
        Session::put('locale', $locale);
        
        // ضبط اللغة الحالية
        App::setLocale($locale);

        // إعادة التوجيه للصفحة السابقة أو للصفحة الرئيسية
        return redirect()->back()->with('success', __('translation.language_changed'));
    }
}


<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    /**
     * Switch language between English and Indonesian.
     */
    public function switch(Request $request, string $locale): RedirectResponse
    {
        if (in_array($locale, ['en', 'id'], true)) {
            $request->session()->put('locale', $locale);
        }

        return redirect()->back(fallback: route('home'));
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\UserLang;

class SetUserLanguage
{
    public function handle(Request $request, Closure $next)
    {
        $lang = 'ru';

        // ✅ 1. Check logged-in user
        $user = auth('api')->user();

        if ($user && $user->device_token && $user->device_type) {
            $lang = UserLang::where('user_id', $user->id)
                ->where('device_token', $user->device_token)
                ->where('device_type', $user->device_type)
                ->value('language') ?? $lang;
        }

        // ✅ 2. Guest user (login, verify OTP, etc.)
        else {
            $lang = $request->header('lang')
                ?? $request->lang
                ?? $request->query('lang')
                ?? UserLang::where('device_token', $request->device_id)
                    ->where('device_type', $request->device_type)
                    ->value('language')
                ?? $lang;
        }

        app()->setLocale($lang);

        return $next($request);
    }
}
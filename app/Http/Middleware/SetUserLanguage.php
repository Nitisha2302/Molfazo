<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\UserLang;

class SetUserLanguage
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth('api')->user();
        $lang = 'ru';

        if ($user && $user->device_token && $user->device_type) {

            $userLang = UserLang::where('user_id', $user->id)
                ->where('device_token', $user->device_token)
                ->where('device_type', $user->device_type)
                ->first();

            if ($userLang) {
                $lang = $userLang->language;
            }
        }

        app()->setLocale($lang);

        return $next($request);
    }
}
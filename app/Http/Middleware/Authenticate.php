<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Authenticate
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (! $user) {
            $sessionUser = $request->session()->get('user');

            if ($sessionUser) {
                $user = User::find($sessionUser->id);

                if ($user) {
                    Auth::login($user);
                }
            }
        }

        if (! $user) {
            $request->session()->forget('user');

            return redirect()
                ->to(route('index').'#login')
                ->with('error', 'Please login first.');
        }

        $request->session()->put('user', $user);

        return $next($request);
    }
}

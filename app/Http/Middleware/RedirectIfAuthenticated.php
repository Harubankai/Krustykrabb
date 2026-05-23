<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
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
            return $next($request);
        }

        $request->session()->put('user', $user);

        return redirect()->route(match ($user->role) {
            'admin' => 'admin.dashboard',
            'instructor' => 'instructor.dashboard',
            'rider' => 'rider.dashboard',
            default => 'customer.dashboard',
        });
    }
}

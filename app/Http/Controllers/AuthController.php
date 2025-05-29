<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function login(): View
    {
        return view('auth.login');
    }

    public function authenticate (Request $request)
    {
        $credentials = $request->validate(
            [
                'username' => ['required', 'min:3', 'max:30'],
                'password' => ['required', 'min:8', 'max:32', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/']
            ],
            [
                'username.required' => "O username é obrigatório",
                'username.min' => "O username deve ter no mínimo :min caracteres",
                'username.max' => "O username deve ter no máximo :max caracteres",
                'password.required' => "A senha é obrigatória",
                'password.min' => "A senha deve ter no mínimo :min caracteres",
                'password.max' => "A senha deve ter no máximo :max caracteres",
                'password.regex' => "A senha deve ter uma letra maiúscula, uma minúscula e um dígito.",
            ]
        );

        $user = User::where('username', $credentials['username'])
            ->where('active', true)
            ->where(function($query) {
                $query->whereNull('blocked_until')
                    ->orWhere('blocked_until', '<=', now());
            })
            ->whereNotNull('email_verified_at')
            ->whereNull('deleted_at')
            ->first();

        if(!$user) {
            return back()
                ->withInput()
                ->with("invalid_login", "Login inválido.");
        }

        if(!password_verify($credentials['password'], $user->password)) {
            return back()
                ->withInput()
                ->with("invalid_login", "Login inválido.");
        }
        
        $user->last_login = Carbon::now();
        $user->blocked_until = null;
        $user->save();
        
        $request->session()->regenerate();
        Auth::login($user);

        return redirect()->intended(route('home'));
    }
}

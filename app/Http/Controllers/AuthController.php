<?php

namespace App\Http\Controllers;

use App\Mail\NewUserConfirmation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(): View
    {
        return view('auth.login');
    }

    public function authenticate (Request $request): RedirectResponse
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

    public function logout(): RedirectResponse
    {
        Auth::logout();

        return redirect()->route('login');
    }

    public function register(): View
    {
        return view('auth.register');
    }

    public function storeUser(Request $request): RedirectResponse|View
    {
        $request->validate(
            [
                'username' => 'required|min:3|max:30|unique:users,username',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:8|max:32|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
                'password_confirmation' => 'required|same:password',                
            ],
            [
                'username.required' => 'O nome do usuário é obrigatório.',
                'username.min' => 'O nome do usuário deve conter no mínimo :min caracteres.',
                'username.max' => 'O nome do usuário deve conter no máximo :max caracteres.',
                'username.unique' => 'Este nome de usuário não pode ser usado.',
                'email.required' => 'O email é obrigatório.',
                'email.email' => 'O email deve ser válido.',
                'email.unique' => 'Este email não pode ser usado.',
                'password.required' => 'A senha é obrigatória.',
                'password.min' => 'A senha deve ter no mínimo :min caracteres.',
                'password.max' => 'A senha deve ter no máximo :max caracteres.',
                'password.regex' => 'A senha deve ter pelo menos uma letra maiúscula, uma letra minúscula e um dígito.',
                'password_confirmation.required' => 'A confirmação de senha é obrigatória.',
                'password_confirmation.same' => 'A confirmação de senha deve ser igual a senha.',
            ]
        );


        $user = new User();
        $user->username = $request->input('username');
        $user->email = $request->input('email');
        $user->password = bcrypt($request->input('password'));
        $user->token = Str::random(64); 

        $confirmation_link = route('register.confirmation', ['token' => $user->token]);

        $result = Mail::to($user->email)->send(new NewUserConfirmation($user->username, $confirmation_link));


        if(!$result) {
            return back()
                ->withInput()
                ->with('server_error', 'Ocorreu um erro ao enviar o email de confirmação');
        }

        $user->save();

        return view('auth.email_sent', ['email' => $user->email]);
    }


    public function registerConfirmation($token): RedirectResponse|View
    {
       
        $user = User::where('token', $token)->first();

        if(!$user) {
            return redirect()->route('login');
        }

        $user->email_verified_at = Carbon::now();
        $user->token = null;
        $user->active = true;
        $user->last_login = Carbon::now();
        $user->save();

        Auth::login($user);

        return view('auth.new_user_confirmation');
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegistrationRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Attempt to authenticate a user using an SPA
     *
     * @param LoginRequest $request
     * @return RedirectResponse
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        if (Auth::attempt([
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        ], $request->input('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'email' => __('auth.failed'),
        ])->onlyInput('email');
    }

    /**
     * Register a new user from the request
     * 
     * @param RegistrationRequest $request
     * @return Response
     */
    public function register(RegistrationRequest $request): Response
    {
        // the data is already verified, just create a new user
        User::create([
            'email' => $request->input('email'),
            'name' => $request->input('name'),
            'password' => Hash::make($request->input('password')),
        ]);
        
        // the user will have to log in after the registration
        return response('OK');
    }

    /**
     * Logout the user from current account
     *
     * @param Request $request
     * @return Response
     */
    public function logout(Request $request): Response
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response('OK');
    }

    /**
     * Confirm the password again
     * 
     * @param Request $request
     * @return RedirectResponse
     */
    public function password(Request $request): RedirectResponse
    {
        if (! Hash::check($request->password, $request->user()->password)) {
            return back()->withErrors([
                'password' => [__('auth.password')],
            ]);
        }

        $request->session()->passwordConfirmed();

        return redirect()->intended();
    }
}

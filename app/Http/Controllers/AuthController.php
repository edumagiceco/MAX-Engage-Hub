<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        if (! Auth::attempt($request->validated(), true)) {
            return back()
                ->withInput($request->safe()->except('password'))
                ->withErrors([
                    'email' => '이메일 또는 비밀번호가 일치하지 않습니다.',
                ]);
        }

        $request->session()->regenerate();

        return redirect()->route('admin.inbox');
    }

    public function destroy(): RedirectResponse
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login');
    }
}

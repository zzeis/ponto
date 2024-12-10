<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function showFirstPasswordChangeForm()
    {
        return view('auth.first-password-change');
    }

    public function updateFirstPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);


        $user = User::find(Auth::id());
        $user->update([
            'password' => Hash::make($request->password),
            'first_login' => false,
        ]);



        return redirect()->route('estagiario.dashboard')->with('success', 'Senha alterada com sucesso!');
    }
}

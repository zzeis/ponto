<?php

namespace App\Http\Controllers;

use App\Models\Departamento;
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



        return redirect()->route($user->nivel_acesso . '.dashboard')->with('success', 'Senha alterada com sucesso!');
    }

    public function viewUserInformations(User $user)
    {

        $departamento = Departamento::find($user->departamento_id);


        return view('user.informations', compact('user', 'departamento'));
    }

    public function update(Request $request, User $user)
    {

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'cpf' => 'sometimes|required|size:11|regex:/^\d+$/|unique:users,cpf,' . $user->id,
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'employee_code' => 'sometimes|required',
            'local' => 'sometimes|required|string',
            'departamento_id' => 'sometimes|nullable|exists:departamentos,id',
            'nivel_acesso' => 'sometimes|in:estagiario,supervisor,admin'
        ]);

        $user->update(array_filter($validated));

        return redirect()->back()->with('success', 'Usuário atualizado com sucesso');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Departamento;
use App\Models\HorariosDefault;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function showFirstPasswordChangeForm()
    {
        return view('auth.first-password-change');
    }

    public function ListEstagiariosUsers(Request $request)
    {

        $query = User::where('nivel_acesso', 'estagiario');

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $estagiarios = $query->paginate(10);
        return view('admin.listaestagiarios', compact('estagiarios'));
    }
    public function listSupervisoresUsers(Request $request)
    {

        $query = User::where('nivel_acesso', 'supervisor');

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $estagiarios = $query->paginate(10);
        return view('admin.listaestagiarios', compact('estagiarios'));
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
        $horarios = HorariosDefault::getHorarioAtualByUser($user->id);


        return view('user.informations', compact('user', 'departamento', 'horarios'));
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
            'nivel_acesso' => 'sometimes|in:estagiario,supervisor,admin',
            // Validação dos horários apenas se for estagiário
            'entrada_manha' => $request->nivel_acesso === 'estagiario' ? 'required|date_format:H:i' : 'sometimes',
            'saida_manha' => $request->nivel_acesso === 'estagiario' ? 'required|date_format:H:i|after:entrada_manha' : 'sometimes',
            'entrada_tarde' => $request->nivel_acesso === 'estagiario' ? 'required|date_format:H:i|after:saida_manha' : 'sometimes',
            'saida_tarde' => $request->nivel_acesso === 'estagiario' ? 'required|date_format:H:i|after:entrada_tarde' : 'sometimes',

        ]);

        DB::transaction(function () use ($validated, $user, $request) {
            // Atualiza dados do usuário
            $user->update(array_filter($validated));

            // Só gerencia horários se for estagiário
            if ($request->nivel_acesso === 'estagiario') {
                $horarioFields = [
                    'entrada_manha',
                    'saida_manha',
                    'entrada_tarde',
                    'saida_tarde'
                ];

                $horario = HorariosDefault::where('user_id', $user->id)->first();

                $horarioData = array_intersect_key($validated, array_flip($horarioFields));

                if ($horario) {
                    $horario->update($horarioData);
                } else {
                    HorariosDefault::create(array_merge(
                        $horarioData,
                        ['user_id' => $user->id]
                    ));
                }
            } else {
                // Se não for estagiário, remove qualquer horário existente
                HorariosDefault::where('user_id', $user->id)->delete();
            }
        });

        return redirect()->back()->with('success', 'Usuário atualizado com sucesso');
    }
}

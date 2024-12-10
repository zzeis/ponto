<?php

namespace App\Http\Controllers;

use App\Models\RegistroPonto;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminDashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard');
    }

    public function listUsersEstagiarios(Request $request)
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

    public function toggleUserStatus(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);
        return back()->with('success', 'Status do usuário atualizado');
    }


    public function verifyHorarios(User $user, Request $request)
    {
        // Calcula o início (dia 15 do mês anterior)
        $dataInicio = now()->subMonth()->day(15);

        // Calcula o fim (dia 16 do mês atual)
        $dataFim = now()->day(16);
        $dataHoje = Carbon::today()->format('Y-m-d');
        // Registros no intervalo especificado para o usuário logado
        $registros = RegistroPonto::where('user_id', $user->id)
            ->whereBetween('data', [$dataInicio, $dataFim])
            ->orderBy('data')
            ->orderBy('hora')
            ->get()
            ->groupBy('data');


        return view('admin.relogioponto.horariosMes', compact('registros', 'user'));
    }


    public function downloadHorariosByMonth(User $user, Request $request)
    {
        // Calcula o início (dia 15 do mês anterior)
        $dataInicio = now()->subMonth()->day(15);

        // Calcula o fim (dia 16 do mês atual)
        $dataFim = now()->day(16);


        // Registros no intervalo especificado para o usuário logado
        $registros = RegistroPonto::where('user_id', $user->id)
            ->whereBetween('data', [$dataInicio, $dataFim])
            ->orderBy('data')
            ->orderBy('hora')
            ->get()
            ->groupBy('data');

        $logoPath = public_path('images/logo.png');
        $logoBase64 = base64_encode(file_get_contents($logoPath));
        $pdf = Pdf::loadView('relogioponto.relatoriomes', [
            'registros' => $registros,
            'user' => $user,
            'logoBase64' => $logoBase64
        ]);
        return $pdf->download('relatorio-pontos-mes.pdf');
    }

    public function createUserview()
    {
        return view('admin.createUser');
    }


    public function createUser(Request $request)
    {
        $cpf = $request->cpf;
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'email|unique:users',
            'cpf' => 'required|size:11|regex:/^\d+$/|unique:users,cpf',
            'departamento_id' => 'nullable|exists:departamentos,id', // Validação para o campo de departamento
        ]);


        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'cpf' => $validated['cpf'],
            'password' => Hash::make($cpf),
            'nivel_acesso' => 'estagiario',
            'first_login' => true,

        ]);

        return back()->with('success', 'Usuário criado com sucesso');
    }
}

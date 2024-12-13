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

        $user =  auth()->user();

        $query = User::where('nivel_acesso', 'estagiario');


        if ($user->nivel_acesso === 'supervisor') {
            $query->where('departamento_id', $user->departamento_id);
        }

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

        // Define o mês e ano selecionados (ou padrão para o mês e ano atual)
        $mesSelecionado = $request->input('mes', now()->month);
        $anoSelecionado = $request->input('ano', now()->year);

        // Calcula as datas de início e fim para o intervalo
        $dataInicio = Carbon::create($anoSelecionado, $mesSelecionado, 15)->startOfDay();
        $dataFim = $dataInicio->copy()->addMonth()->day(16)->endOfDay();

        // Filtra os registros no intervalo de datas
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
        return view('user.createUser');
    }



    public function createUser(Request $request)
    {
        $cpf = $request->cpf;
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'local' => 'string|max:255',
            'email' => 'email|unique:users',
            'cpf' => 'required|size:11|regex:/^\d+$/|unique:users,cpf',
            'employee_code' => 'required|regex:/^\d+$/|unique:users,employee_code',
            'departamento_id' => 'nullable|exists:departamentos,id', // Validação para o campo de departamento
        ]);

        $nivelAcesso = $request->get('nivel_acesso', 'estagiario');
        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'cpf' => $validated['cpf'],
            'employee_code' => $validated['employee_code'],
            'local' => $validated['local'],
            'departamento_id' => $validated['departamento_id'],
            'password' => Hash::make($cpf),
            'nivel_acesso' => $nivelAcesso,
            'first_login' => true,
        ]);

        return back()->with('success', 'Usuário criado com sucesso');
    }
}

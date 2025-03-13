<?php

namespace App\Http\Controllers;

use App\Models\HorariosDefault;
use App\Models\HorarioTrabalho;
use App\Models\RegistroPonto;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        
        // Calcula horas extras
        $horasExtrasTotal = 0;
        $diasComHorasExtras = 0;
        $detalhamentoHorasExtras = [];
        
        foreach ($registros as $data => $registrosDoDia) {
            // Verifica quantos registros temos no dia
            $qtdRegistros = count($registrosDoDia);
            $horasTrabalhadas = 0;
            
            if ($qtdRegistros == 4) {
                // Caso padrão: quatro batidas
                $registrosPorTipo = [];
                foreach ($registrosDoDia as $registro) {
                    $registrosPorTipo[$registro->tipo] = $registro->hora;
                }
                
                // Verifica se temos todos os quatro registros necessários
                if (isset($registrosPorTipo['entrada_manha']) && 
                    isset($registrosPorTipo['saida_almoco']) && 
                    isset($registrosPorTipo['retorno_almoco']) && 
                    isset($registrosPorTipo['saida_fim'])) {
                    
                    // Calcula as horas trabalhadas no dia (manhã + tarde)
                    $horasTrabalhadas = $this->calcularHorasTrabalhadasQuatroBatidas(
                        $data,
                        $registrosPorTipo['entrada_manha'],
                        $registrosPorTipo['saida_almoco'],
                        $registrosPorTipo['retorno_almoco'],
                        $registrosPorTipo['saida_fim']
                    );
                }
            }
            elseif ($qtdRegistros == 2) {
                // Caso alternativo: apenas duas batidas (entrada e saída no mesmo dia)
                $entrada = Carbon::parse($data . ' ' . $registrosDoDia[0]->hora);
                $saida = Carbon::parse($data . ' ' . $registrosDoDia[1]->hora);
                
                // Calcula a diferença em minutos
                $totalMinutos = $entrada->diffInMinutes($saida);
                $horasTrabalhadas = $totalMinutos / 60;
            }
            // Pode adicionar mais casos conforme necessário
            
            // Verifica se trabalhou mais de 6 horas no dia
            if ($horasTrabalhadas > 6) {
                $horasExtras = $horasTrabalhadas - 6;
                $horasExtrasTotal += $horasExtras;
                $diasComHorasExtras++;
                
                // Guarda detalhamento para exibição
                $detalhamentoHorasExtras[] = [
                    'data' => Carbon::parse($data)->format('d/m/Y'),
                    'horasTrabalhadas' => number_format($horasTrabalhadas, 2),
                    'horasExtras' => number_format($horasExtras, 2)
                ];
            }
        }
        
        // Converte horas extras em dias se necessário
        $diasExtras = 0;
        $horasExtrasRestantes = 0;
        
        if ($horasExtrasTotal >= 24) {
            $diasExtras = floor($horasExtrasTotal / 24);
            $horasExtrasRestantes = $horasExtrasTotal % 24;
        } else {
            $horasExtrasRestantes = $horasExtrasTotal;
        }
        
        return view('admin.relogioponto.horariosMes', compact(
            'registros', 
            'user', 
            'horasExtrasTotal',
            'diasComHorasExtras',
            'diasExtras',
            'horasExtrasRestantes',
            'detalhamentoHorasExtras'
        ));
    }
    
    /**
     * Calcula horas trabalhadas com base nas quatro batidas do dia
     * 
     * @param string $data Data do registro
     * @param string $entradaManha Hora de entrada de manhã
     * @param string $saidaAlmoco Hora de saída para almoço
     * @param string $retornoAlmoco Hora de retorno do almoço
     * @param string $saidaFim Hora de saída no fim do dia
     * @return float
     */
    private function calcularHorasTrabalhadasQuatroBatidas($data, $entradaManha, $saidaAlmoco, $retornoAlmoco, $saidaFim)
    {
        // Converte as strings de hora para objetos Carbon
        $entrada1 = Carbon::parse($data . ' ' . $entradaManha);
        $saida1 = Carbon::parse($data . ' ' . $saidaAlmoco);
        $entrada2 = Carbon::parse($data . ' ' . $retornoAlmoco);
        $saida2 = Carbon::parse($data . ' ' . $saidaFim);
        
        // Calcula a diferença em minutos para cada período
        $minutosPeriodo1 = $saida1->diffInMinutes($entrada1);
        $minutosPeriodo2 = $saida2->diffInMinutes($entrada2);
        
        // Soma os minutos totais trabalhados
        $totalMinutos = $minutosPeriodo1 + $minutosPeriodo2;
        
        // Converte minutos para horas decimais
        return $totalMinutos / 60;
    }


    public function downloadHorariosByMonth(User $user, Request $request)
    {
        // Calcula o início (dia 15 do mês anterior)
        $dataInicio = now()->subMonth()->day(15);

        // Calcula o fim (dia 16 do mês atual)
        $dataFim = now()->day(15);


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
        $nivelAcesso = $request->get('nivel_acesso', 'estagiario');

        $rules = [
            'name' => 'required|string|max:255',
            'local' => 'string|max:255',
            'email' => 'email|unique:users',
            'cpf' => 'required|size:11|regex:/^\d+$/|unique:users,cpf',
            'employee_code' => 'required|regex:/^\d+$/|unique:users,employee_code',
            'departamento_id' => 'nullable|exists:departamentos,id',

        ];

        // Regras adicionais para estagiários
        if ($nivelAcesso === 'estagiario') {
            $rules = array_merge($rules, [
                'entrada_manha' => 'required|date_format:H:i',
                'saida_manha' => 'required|date_format:H:i|after:entrada_manha',
                'entrada_tarde' => 'required|date_format:H:i|after:saida_manha',
                'saida_tarde' => 'required|date_format:H:i|after:entrada_tarde',
            ]);
        }
        $validated = $request->validate($rules);

        DB::transaction(function () use ($validated, $nivelAcesso, $cpf) {
            $user = User::create([
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

            //so criar a tabela horarios se o nivel de acesso
            //for estagiario
            if ($nivelAcesso === 'estagiario') {
                HorariosDefault::create([
                    'user_id' => $user->id,
                    'entrada_manha' => $validated['entrada_manha'],
                    'saida_manha' => $validated['saida_manha'],
                    'entrada_tarde' => $validated['entrada_tarde'],
                    'saida_tarde' => $validated['saida_tarde'],
                    'data_inicio' => now()
                ]);
            }
        });

        return back()->with('success', 'Usuário criado com sucesso');
    }
}

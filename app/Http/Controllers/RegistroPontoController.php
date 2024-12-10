<?php

namespace App\Http\Controllers;

use App\Models\RegistroPonto;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegistroPontoController extends Controller
{
    public function index()
    {
        // Calcula o início (dia 15 do mês anterior)
        $dataInicio = now()->subMonth()->day(15);

        // Calcula o fim (dia 16 do mês atual)
        $dataFim = now()->day(16);
        $dataHoje = Carbon::today()->format('Y-m-d');
        // Registros no intervalo especificado para o usuário logado
        $registros = RegistroPonto::where('user_id', auth()->id())
            ->whereBetween('data', [$dataInicio, $dataFim])
            ->orderBy('data')
            ->orderBy('hora')
            ->get()
            ->groupBy('data');

        $registrosHoje = RegistroPonto::where('user_id', auth()->id())
            ->whereDate('data', $dataHoje)
            ->pluck('tipo')
            ->toArray();

        return view('relogioponto.index', compact('registros', 'registrosHoje'));
    }
    public function relatoriomes()
    {
        // Calcula o início (dia 15 do mês anterior)
        $dataInicio = now()->subMonth()->day(15);

        // Calcula o fim (dia 16 do mês atual)
        $dataFim = now()->day(16);

        // Garanta que o usuário está autenticado
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        $user = Auth::user();
        // Registros no intervalo especificado para o usuário logado
        $registros = RegistroPonto::where('user_id', auth()->id())
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

        //  return view('relogioponto.relatoriomes', compact('registros', 'user'));
    }



    public function downloadRegistrosByUser(User $user, Request $request)
    {
        $mes = $request->mes ?? now()->month;
        $ano = $request->ano ?? now()->year;

        // Defina o início (dia 15 do mês anterior)
        $dataInicio = now()->setYear($ano)->setMonth($mes)->subMonthNoOverflow()->setDay(15)->startOfDay();

        // Defina o fim (dia 16 do mês atual)
        $dataFim = now()->setYear($ano)->setMonth($mes)->setDay(16)->endOfDay();

        // Registros no intervalo
        $registros = RegistroPonto::where('user_id', $user->id)
            ->whereBetween('data', [$dataInicio, $dataFim])
            ->orderBy('data')
            ->orderBy('hora')
            ->get()
            ->groupBy('data');

        $observacoes = RegistroPonto::where('user_id', $user->id)
            ->whereBetween('data', [$dataInicio, $dataFim])
            ->whereNotNull('observacao')
            ->orderBy('data')
            ->pluck('observacao', 'data'); // Retorna as observações associadas às datas

        $logoPath = public_path('images/logo.png');
        $logoBase64 = base64_encode(file_get_contents($logoPath));
        $pdf = Pdf::loadView('relogioponto.relatoriomes', [
            'registros' => $registros,
            'user' => $user,
            'logoBase64' => $logoBase64,
            'observacoes' => $observacoes
        ]);

        return $pdf->download('relatorio-pontos-mes.pdf');
    }

    public function registrar($tipo)
    {
        // Validar tipos de registro
        $tiposValidos = [
            'entrada_manha',
            'saida_almoco',
            'retorno_almoco',
            'saida_fim'
        ];

        if (!in_array($tipo, $tiposValidos)) {
            return back()->with('error', 'Tipo de registro inválido');
        }

        // Verificar se já existe registro deste tipo hoje
        if (RegistroPonto::existeRegistroHoje($tipo)) {
            return back()->with('error', 'Registro já efetuado para este período');
        }

        // Criar registro de ponto
        RegistroPonto::create([
            'user_id' => auth()->id(),
            'data' => now()->format('Y-m-d'),
            'tipo' => $tipo,
            'hora' => now()->format('H:i:s')
        ]);

        return back()->with('success', 'Registro de ponto efetuado com sucesso');
    }

    // Método para admin ver registros de todos os funcionários
    public function adminIndex(Request $request)
    {
        $this->authorize('viewAny', RegistroPonto::class);

        $registros = RegistroPonto::with('user')
            ->when($request->mes, function ($query) use ($request) {
                return $query->whereMonth('data', $request->mes);
            })
            ->when($request->ano, function ($query) use ($request) {
                return $query->whereYear('data', $request->ano);
            })
            ->orderBy('data')
            ->orderBy('hora')
            ->get()
            ->groupBy(['user.name', 'data']);

        return view('admin.registros_ponto', compact('registros'));
    }

    public function salvarObservacao(Request $request, $data)
    {
        $request->validate([
            'observacao' => 'nullable|string',
        ]);

        // Atualize ou crie a observação no registro do dia
        $registro = RegistroPonto::where('data', $data)->first();

        if ($registro) {
            $registro->observacao = $request->observacao;
            $registro->save();
        }

        return back()->with('success', 'Observação salva com sucesso.');
    }

    // Gerar relatório consolidado
    public function gerarRelatorio(Request $request)
    {
        $this->authorize('viewAny', RegistroPonto::class);

        $relatorio = RegistroPonto::select(
            'user_id',
            DB::raw('MONTH(data) as mes'),
            DB::raw('YEAR(data) as ano')
        )
            ->with('user')
            ->groupBy('user_id', 'mes', 'ano')
            ->get()
            ->map(function ($item) {
                // Calcular horas trabalhadas, atrasos, etc.
                $registrosMes = RegistroPonto::where('user_id', $item->user_id)
                    ->whereMonth('data', $item->mes)
                    ->whereYear('data', $item->ano)
                    ->get();

                return [
                    'usuario' => $item->user->name,
                    'mes' => $item->mes,
                    'ano' => $item->ano,
                    // Adicionar mais métricas conforme necessário
                ];
            });

        return view('admin.relatorio_ponto', compact('relatorio'));
    }
}

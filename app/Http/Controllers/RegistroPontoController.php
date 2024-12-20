<?php

namespace App\Http\Controllers;

use App\Jobs\RegistrarPontoJob;
use App\Models\RegistroPonto;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RegistroPontoController extends Controller
{

    private $tiposValidos = [
        'entrada_manha',
        'saida_almoco',
        'retorno_almoco',
        'saida_fim'
    ];

    public function index()
    {


        // Define o mês e ano selecionados (ou padrão para o mês e ano atual)
        $mesSelecionado = now()->month;
        $anoSelecionado = now()->year;

        // Calcula as datas de início e fim para o intervalo
        $dataInicio = Carbon::create($anoSelecionado, $mesSelecionado, 15)->startOfDay();
        $dataFim = $dataInicio->copy()->addMonth()->day(16)->endOfDay();

        // Calcula o fim (dia 16 do mês atual)

        $dataHoje = Carbon::today()->format('Y-m-d');
        // Registros no intervalo especificado para o usuário logado
        $registros = RegistroPonto::where('user_id', auth()->id())
            ->whereBetween('data', [$dataInicio, $dataFim])
            ->orderBy('data', 'desc')
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
        // Define o mês e ano selecionados (ou padrão para o mês e ano atual)
        $mesSelecionado = now()->month;
        $anoSelecionado = now()->year;

        // Calcula as datas de início e fim para o intervalo
        $dataInicio = Carbon::create($anoSelecionado, $mesSelecionado, 15)->startOfDay();
        $dataFim = $dataInicio->copy()->addMonth()->day(16)->endOfDay();


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

        try {

            // Dados do registro
            $data = [
                'user_id' => auth()->id(),
                'data' => now()->format('Y-m-d'),
                'tipo' => $tipo,
                'hora' => now()->format('H:i:s')
            ];
            // Validar tipos de registro
            if (!in_array($tipo, $tiposValidos)) {
                return response()->json([
                    'error' => 'Tipo de registro inválido'
                ], 400);
            }


            // Verificar se já existe registro deste tipo hoje
            if (RegistroPonto::existeRegistroHoje($tipo)) {
                return response()->json([
                    'error' => 'Registro já efetuado para este período'
                ], 400);
            }

            // Verificar intervalo mínimo entre registros
            if (!$this->verificarIntervaloRegistro($tipo)) {
                return response()->json([
                    'error' => 'A tentativa de registro está fora do horário padronizado'
                ], 400);
            }

            // Enviar job para a fila "pontos"
            RegistrarPontoJob::dispatch($data)->onQueue('pontos');

            return response()->json([
                'success' => 'Registro de ponto enviado para processamento',
                'proximoTipo' => $this->obterProximoTipo($tipo)
            ]);
        } catch (\Exception $e) {
            Log::error('Erro no registro de ponto: ' . $e->getMessage());
            return response()->json([
                'error' => 'Erro interno ao processar registro'
            ], 500);
        }
    }

    private function verificarIntervaloRegistro($tipo)
    {

        if ($tipo === 'entrada_manha') {
            return true;
        }

        // Buscar o último registro do usuário de qualquer tipo
        $ultimoRegistro = RegistroPonto::where('user_id', auth()->id())
            ->where('data', now()->format('Y-m-d'))
            ->orderBy('created_at', 'desc')
            ->first();

        // Se não há registro anterior, permite o registro
        if (!$ultimoRegistro) {
            return true;
        }

        // Calcular o intervalo entre o último registro e o atual
        $ultimoRegistroDateTime = Carbon::createFromFormat(
            'Y-m-d H:i:s',
            $ultimoRegistro->data . ' ' . $ultimoRegistro->hora
        );

        $diferencaMinutos = now()->diffInMinutes($ultimoRegistroDateTime);

        // Verifica se passou 1 hora e 30 minutos (90 minutos)
        return $diferencaMinutos >= 90;
    }

    public function atualizarRegistros(Request $request)
    {
        try {

            $mesSelecionado = now()->month;
            $anoSelecionado = now()->year;

            // Calcula as datas de início e fim para o intervalo
            $dataInicio = Carbon::create($anoSelecionado, $mesSelecionado, 15)->startOfDay();
            $dataFim = $dataInicio->copy()->addMonth()->day(16)->endOfDay();

            $tipo = $request->get('tipo');
            $registros = RegistroPonto::where('user_id', auth()->id())
                ->whereBetween('data', [$dataInicio, $dataFim])
                ->orderBy('data')
                ->orderBy('hora')
                ->get()
                ->groupBy('data');

            // Verifique se o registro do tipo atual está presente
            $registroEncontrado = RegistroPonto::where('user_id', auth()->id())
                ->where('data', now()->format('Y-m-d'))
                ->where('tipo', $tipo)
                ->exists();

            $html = view('components.registros-tabela', compact('registros'))->render();

            return response()->json([
                'success' => true,
                'html' => $html,
                'registroEncontrado' => $registroEncontrado
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar registros: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Erro ao atualizar registros'
            ], 500);
        }
    }


    private function obterProximoTipo($tipoAtual)
    {
        $indice = array_search($tipoAtual, $this->tiposValidos);
        return $indice !== false && $indice < count($this->tiposValidos) - 1
            ? $this->tiposValidos[$indice + 1]
            : null;
    }



    public function updateBatch(Request $request, User $user)
    {

        // Verifica se o departamento do usuário logado é o mesmo do usuário associado ao registro

        if (
            auth()->user()->nivel_acesso !== 'admin' &&
            auth()->user()->departamento_id !== $user->departamento_id
        ) {
            return back()->with('error', 'Você não tem permissão para alterar os registros deste usuário.');
        }


        $registros = $request->input('registros', []);



        foreach ($registros as $data => $registro) {


            // Entry Morning

            $this->updateOrCreateRegistro($user, $data, 'entrada_manha', $registro['entrada_manha'] ?? null);

            // Lunch Exit
            $this->updateOrCreateRegistro($user, $data, 'saida_almoco', $registro['saida_almoco'] ?? null);

            // Lunch Return
            $this->updateOrCreateRegistro($user, $data, 'retorno_almoco', $registro['retorno_almoco'] ?? null);

            // Final Exit
            $this->updateOrCreateRegistro($user, $data, 'saida_fim', $registro['saida_fim'] ?? null);

            // Observation
            $this->updateObservacao($user, $data, $registro['observacao'] ?? null);
        }

        return redirect()->back()->with('success', 'Registros atualizados com sucesso');
    }

    private function updateOrCreateRegistro(User $user, string $data, string $tipo, ?string $hora)
    {
        if ($hora) {
            RegistroPonto::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'data' => $data,
                    'tipo' => $tipo
                ],
                [
                    'hora' => $hora
                ]
            );
        }
    }

    private function updateObservacao(User $user, string $data, ?string $observacao)
    {
        RegistroPonto::where('user_id', $user->id)
            ->where('data', $data)
            ->update(['observacao' => $observacao]);
    }

    public function salvarObservacao(Request $request, $data)
    {
        $request->validate([
            'observacao' => 'nullable|string',
        ]);

        // Obtém o registro do ponto para a data fornecida
        $registro = RegistroPonto::where('data', $data)->first();

        if ($registro) {
            // Obtém o usuário associado ao registro
            $usuarioRegistro = User::find($registro->user_id);



            // Atualiza a observação se a validação passar
            $registro->observacao = $request->observacao;
            $registro->save();

            return back()->with('success', 'Observação salva com sucesso.');
        }

        return back()->with('error', 'Registro não encontrado.');
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

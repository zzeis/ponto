<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\RegistroPonto;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RegistrarPontoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // Número de tentativas de processamento
    public $tries = 3;

    // Tempo de espera entre tentativas (em segundos)
    public $backoff = 5;

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function handle()
    {
        try {
            // Iniciar transação para garantir consistência
            DB::beginTransaction();

            // Log de início do processamento
            Log::info('Iniciando processamento de registro de ponto', [
                'data' => $this->data
            ]);

            // Criar registro com validações adicionais
            $registro = RegistroPonto::create($this->data);

            // Log de sucesso
            Log::info('Registro de ponto criado com sucesso', [
                'id' => $registro->id,
                'tipo' => $this->data['tipo'] ?? 'não especificado'
            ]);

            // Confirmar transação
            DB::commit();

            return $registro;
        } catch (\Exception $e) {
            // Reverter transação em caso de erro
            DB::rollBack();

            // Log de erro detalhado
            Log::error('Erro ao processar registro de ponto', [
                'error' => $e->getMessage(),
                'data' => $this->data,
                'trace' => $e->getTraceAsString()
            ]);

            // Relançar exceção para permitir tratamento pelo queue worker
            throw $e;
        }
    }

    // Método chamado se o job falhar após todas as tentativas
    public function failed(\Exception $exception)
    {
        Log::critical('Falha definitiva no processamento de registro de ponto', [
            'error' => $exception->getMessage(),
            'data' => $this->data
        ]);
    }
}

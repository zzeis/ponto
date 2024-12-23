<?php
// app/Services/WorkingHoursService.php
namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class WorkingHoursService
{
    public function getHours(int $employeeId, ?Carbon $date = null)
    {
        $date = $date ?? Carbon::now(); // Usa a data atual caso nenhuma data seja fornecida.

        $dailyHours = $this->calculateDailyHours($employeeId, $date); // Calcula horas diárias.
        $monthlyHours = $this->calculateMonthlyHours($employeeId, $date); // Calcula horas mensais.

        return [
            'daily' => $dailyHours,
            'monthly' => $monthlyHours
        ];
    }

    /**
     * Calcula as horas trabalhadas em um dia específico.
     *
     * @param int $employeeId
     *        ID do funcionário.
     * @param Carbon $date
     *        Data para calcular as horas diárias.
     *
     * @return float
     *         Retorna o total de horas trabalhadas no dia.
     */

    private function calculateDailyHours(int $employeeId, Carbon $date)
    {
        // Obtém os registros de ponto do dia para o funcionário.
        $times = DB::table('registros_ponto')
            ->where('user_id', $employeeId)
            ->where('data', $date->format('Y-m-d'))
            ->get()
            ->pluck('hora', 'tipo')
            ->toArray();

        // Se não houver registros, retorna 0.
        if (empty($times)) {
            return 0;
        }

        // Obtém os horários específicos do dia, se disponíveis.
        $morningStart = isset($times['entrada_manha']) ? Carbon::parse($times['entrada_manha']) : null;
        $lunchStart = isset($times['saida_almoco']) ? Carbon::parse($times['saida_almoco']) : null;
        $lunchEnd = isset($times['retorno_almoco']) ? Carbon::parse($times['retorno_almoco']) : null;
        $dayEnd = isset($times['saida_fim']) ? Carbon::parse($times['saida_fim']) : null;

        // Se algum horário estiver faltando, calcula horas parciais.
        if (!$morningStart || !$lunchStart || !$lunchEnd || !$dayEnd) {
            return $this->calculatePartialHours($morningStart, $lunchStart, $lunchEnd, $dayEnd);
        }

        // Calcula as horas da manhã e da tarde, em segundos.
        $morningHours = $lunchStart->diffInSeconds($morningStart);
        $afternoonHours = $dayEnd->diffInSeconds($lunchEnd);

        // Retorna o total de horas trabalhadas no dia, convertendo para horas.
        return round(($morningHours + $afternoonHours) / 3600, 2);
    }

    /**
     * Calcula as horas parciais caso os horários de entrada/saída estejam incompletos.
     *
     * @param Carbon|null $morningStart
     *        Hora de início da manhã.
     * @param Carbon|null $lunchStart
     *        Hora de saída para o almoço.
     * @param Carbon|null $lunchEnd
     *        Hora de retorno do almoço.
     * @param Carbon|null $dayEnd
     *        Hora de saída final.
     *
     * @return float
     *         Retorna o total de horas trabalhadas até o momento.
     */

    private function calculatePartialHours($morningStart, $lunchStart, $lunchEnd, $dayEnd)
    {
        $now = Carbon::now();
        $total = 0;

        // Calcula as horas da manhã, se disponíveis.
        if ($morningStart) {
            if ($lunchStart) {
                $total += $lunchStart->diffInSeconds($morningStart);
            } else {
                $total += $now->diffInSeconds($morningStart);
            }
        }

        // Calcula as horas da tarde, se disponíveis.
        if ($lunchEnd) {
            if ($dayEnd) {
                $total += $dayEnd->diffInSeconds($lunchEnd);
            } else {
                $total += $now->diffInSeconds($lunchEnd);
            }
        }

        // Retorna o total de horas convertidas para horas.
        return round($total / 3600, 2);
    }

    /**
     * Calcula as horas trabalhadas no mês atual.
     *
     * @param int $employeeId
     *        ID do funcionário.
     * @param Carbon $date
     *        Data base para o cálculo.
     *
     * @return float
     *         Retorna o total de horas trabalhadas no mês.
     */
    private function calculateMonthlyHours(int $employeeId, Carbon $date)
    {


        $mesSelecionado = now()->month;
        $anoSelecionado = now()->year;

        // Calcula as datas de início e fim para o intervalo
        // Define o intervalo de 15 do mês atual até 16 do próximo mês.
        $startOfMonth  = Carbon::create($anoSelecionado, $mesSelecionado, 15)->startOfDay();
        $endOfMonth = $startOfMonth->copy()->addMonth()->day(16)->endOfDay();

        $monthlyHours = 0;
        $currentDate = $startOfMonth;

        // Calcula as horas trabalhadas em cada dia do intervalo.
        while ($currentDate <= $endOfMonth && $currentDate <= Carbon::now()) {
            $monthlyHours += $this->calculateDailyHours($employeeId, $currentDate);
            $currentDate->addDay();
        }

        // Retorna o total de horas mensais, arredondado.
        return round($monthlyHours, 2);
    }
}

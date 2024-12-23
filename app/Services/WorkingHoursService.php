<?php
// app/Services/WorkingHoursService.php
namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class WorkingHoursService
{
    public function getHours(int $employeeId, ?Carbon $date = null)
    {
        $date = $date ?? Carbon::now();

        $dailyHours = $this->calculateDailyHours($employeeId, $date);
        $monthlyHours = $this->calculateMonthlyHours($employeeId, $date);

        return [
            'daily' => $dailyHours,
            'monthly' => $monthlyHours
        ];
    }

    private function calculateDailyHours(int $employeeId, Carbon $date)
    {
        $times = DB::table('registros_ponto')
            ->where('user_id', $employeeId)
            ->where('data', $date->format('Y-m-d'))
            ->get()
            ->pluck('hora', 'tipo')
            ->toArray();

        if (empty($times)) {
            return 0;
        }

        $morningStart = isset($times['entrada_manha']) ? Carbon::parse($times['entrada_manha']) : null;
        $lunchStart = isset($times['saida_almoco']) ? Carbon::parse($times['saida_almoco']) : null;
        $lunchEnd = isset($times['retorno_almoco']) ? Carbon::parse($times['retorno_almoco']) : null;
        $dayEnd = isset($times['saida_fim']) ? Carbon::parse($times['saida_fim']) : null;

        if (!$morningStart || !$lunchStart || !$lunchEnd || !$dayEnd) {
            return $this->calculatePartialHours($morningStart, $lunchStart, $lunchEnd, $dayEnd);
        }

        $morningHours = $lunchStart->diffInSeconds($morningStart);
        $afternoonHours = $dayEnd->diffInSeconds($lunchEnd);

        return round(($morningHours + $afternoonHours) / 3600, 2);
    }

    private function calculatePartialHours($morningStart, $lunchStart, $lunchEnd, $dayEnd)
    {
        $now = Carbon::now();
        $total = 0;

        if ($morningStart) {
            if ($lunchStart) {
                $total += $lunchStart->diffInSeconds($morningStart);
            } else {
                $total += $now->diffInSeconds($morningStart);
            }
        }

        if ($lunchEnd) {
            if ($dayEnd) {
                $total += $dayEnd->diffInSeconds($lunchEnd);
            } else {
                $total += $now->diffInSeconds($lunchEnd);
            }
        }

        return round($total / 3600, 2);
    }

    private function calculateMonthlyHours(int $employeeId, Carbon $date)
    {
      

        $mesSelecionado = now()->month;
        $anoSelecionado = now()->year;

        // Calcula as datas de início e fim para o intervalo
        $startOfMonth  = Carbon::create($anoSelecionado, $mesSelecionado, 15)->startOfDay();
        $endOfMonth= $startOfMonth->copy()->addMonth()->day(16)->endOfDay();

        $monthlyHours = 0;
        $currentDate = $startOfMonth;

        while ($currentDate <= $endOfMonth && $currentDate <= Carbon::now()) {
            $monthlyHours += $this->calculateDailyHours($employeeId, $currentDate);
            $currentDate->addDay();
        }

        return round($monthlyHours, 2);
    }
}

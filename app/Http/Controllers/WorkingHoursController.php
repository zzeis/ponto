<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WorkingHoursService
{

    private $workingHoursService;

    public function __construct(WorkingHoursService $workingHoursService)
    {
        $this->workingHoursService = $workingHoursService;
    }

    public function index(Request $request)
    {
        $employeeId = auth()->id(); // ou outro método para obter o ID do funcionário
        $hours = $this->workingHoursService->getHours($employeeId);

        return view('relogioponto.horarios', compact('hours'));
    }

}
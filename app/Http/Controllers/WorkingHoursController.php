<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WorkingHoursService
{

    // Propriedade para armazenar uma instância de WorkingHoursService.
    private $workingHoursService;

      /**
     * Construtor da classe.
     *
     * @param WorkingHoursService $workingHoursService
     *        Uma instância de WorkingHoursService injetada automaticamente pelo container de serviço do Laravel.
     */



    public function __construct(WorkingHoursService $workingHoursService)
    {
        $this->workingHoursService = $workingHoursService;
    }


    /**
     * Método responsável por exibir a página inicial do gerenciamento de horas de trabalho.
     *
     * @param Request $request
     *        O objeto Request contendo os dados da requisição HTTP atual.
     *
     * @return \Illuminate\View\View
     *         Retorna uma view que será renderizada para o usuário.
     */


    public function index(Request $request)
    {
        $employeeId = auth()->id(); // ou outro método para obter o ID do funcionário

        // Chama o método getHours na instância de 
        //WorkingHoursService para buscar as horas do funcionário.
        $hours = $this->workingHoursService->getHours($employeeId);

        return view('relogioponto.horarios', compact('hours'));
    }

}
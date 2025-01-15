<?php

namespace App\Livewire;

use App\Services\WorkingHoursService;
use Livewire\Component;

class WorkingHoursProgress extends Component
{


    public $dailyHours = 0;
    public $monthlyHours = 0;
    public $progressPercentage = 0;
    
    protected $listeners = ['refreshHours' => 'updateHours'];
    
    public function mount(WorkingHoursService $service)
    {
        $this->updateHours($service);// Atualiza as horas ao carregar o componente.
    }
    
    public function updateHours(WorkingHoursService $service)
    {
        // Obtém as horas trabalhadas do serviço.
        $hours = $service->getHours(auth()->id());
        
        $this->dailyHours = $hours['daily'];
        $this->monthlyHours = $hours['monthly'];
        $this->progressPercentage = min(($this->dailyHours / 6) * 100, 100);
    }

    public function render()
    {
        return view('livewire.working-hours-progress');
    }
}

<?php

namespace App\Policies;

use App\Models\User;

class RegistroPontoPolicy
{
    /**
     * Create a new policy instance.
     */
   
        public function viewAny(User $user)
        {
            return $user->nivel_acesso === 'admin';
        }
    
        public function create(User $user)
        {
            return true; // Todos podem registrar ponto
        }
    
}

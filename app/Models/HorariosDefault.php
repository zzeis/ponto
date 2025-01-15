<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HorariosDefault extends Model
{
    protected $table = 'horarios_trabalho';

    protected $fillable = [
        'user_id',
        'entrada_manha',
        'saida_manha',
        'entrada_tarde',
        'saida_tarde',
       
    ];

    protected $casts = [
    
        'entrada_manha' => 'datetime',
        'saida_manha' => 'datetime',
        'entrada_tarde' => 'datetime',
        'saida_tarde' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function getHorarioAtualByUser($userId)
    {
        return static::where('user_id', $userId)->first();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistroPonto extends Model
{
    use HasFactory;
    protected $table = 'registros_ponto';

    protected $fillable = [
        'user_id',
        'data',
        'tipo',
        'hora'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Método para verificar se já existe registro do tipo no dia
    public static function existeRegistroHoje($tipo)
    {
        return self::where('user_id', auth()->id())
            ->where('data', now()->format('Y-m-d'))
            ->where('tipo', $tipo)
            ->exists();
    }
}

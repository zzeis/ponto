<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Notifications\CustomResetPasswordNotification;
use App\Notifications\CustomVerifyEmailNotification;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'cpf',
        'departamento_id',
        'nivel_acesso',
        'first_login',
        'is_active',
        'secretaria',
        'local',
        'employee_code'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }

    public function horarioTrabalho()
    {
        return $this->hasOne(HorariosDefault::class);
    }

    public function horarioAtual()
    {
        return $this->horarioTrabalho()
            ->where('data_inicio', '<=', now())
            ->where(function ($query) {
                $query->whereNull('data_fim')
                    ->orWhere('data_fim', '>=', now());
            });
    }

    public function registros()
    {
        return $this->hasMany(RegistroPonto::class, 'user_id');
    }



    public function sendPasswordResetNotification($token)
    {
        $this->notify((new CustomResetPasswordNotification($token))->onQueue('redis'));
    }
    public function sendEmailVerificationNotification()
    {
        $this->notify((new VerifyEmailNotification())->onQueue('redis'));
    }
}

<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\IdType;
use App\Services\IdService;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'zap_message_counter'
    ];

    protected $hidden = [
        'id',
        'password',
        'remember_token',
        'role',
        'zap_message_counter'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'zap_message_counter' => 'array'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($user) {
            $user->public_id = IdService::create(IdType::USER);
        });
    }

	public function forms()
    {
        return $this->hasMany(Form::class);
    }

    /**
     * funções extras
     */

    public function canSendZapMessage() : bool
    {
        $currentMonth = now()->format('Y-m');

        return isset($this->zap_message_counter[$currentMonth])
        ? $this->zap_message_counter[$currentMonth] < 10
        : true;
    }

    public function incrementAmountZapMessagesSentPerMonth(): void
    {
        $currentMonth = now()->format('Y-m');

        $zapMessageCounter = $this->zap_message_counter ?? [];

        $zapMessageCounter[$currentMonth] = ($zapMessageCounter[$currentMonth] ?? 0) + 1;

        $this->zap_message_counter = $zapMessageCounter;

        // Salva o modelo
        $this->save();
    }
}

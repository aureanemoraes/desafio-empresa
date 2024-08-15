<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Form;
use App\Enums\IdType;
use App\Models\Answer;
use App\Services\IdService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Respondent extends Model
{
    use HasFactory;

    protected $casts = [
        "completed_at" => "datetime"
    ];

    protected $fillable = ['form_id', 'email'];
    protected $hidden = ['id'];

	public function form()
    {
		return $this->belongsTo(Form::class, 'form_id', 'slug');
    }

	public function answers()
    {
        return $this->hasMany(Answer::class, 'respondent_id', 'public_id');
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($user) {
            $user->public_id = IdService::create(IdType::RESPONDENT);
        });
    }

    public function setAsCompleted() : Respondent
    {
        $now = Carbon::now();
        $this->completed_at = $now;
        $this->updated_at = $now;
        $this->save();

        return $this;
    }
}

<?php

namespace App\Models;

use App\Models\User;
use App\Enums\IdType;
use App\Classes\Address;
use App\Models\Respondent;
use App\Services\IdService;
use App\Services\FormService;
use App\Casts\NotificationConfig;
use App\Models\Scopes\TenantScope;
use App\Enums\NotificationResource;
use App\Enums\NotificationContentType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Form extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'slug', 'title', 'fields', 'notifications_config'];
    protected $casts = ['fields' => 'array', 'notifications_config' => NotificationConfig::class];
    protected $hidden = ['id'];

    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope()); // não conhecia

        static::creating(function ($form) {
            $form->slug = IdService::create(IdType::FORM);
            $form->fields = FormService::addIdsToFieldItems($form->fields);
        });

        static::updating(function ($form) {
            $form->fields = FormService::addIdsToFieldItems($form->fields);
        });
    }

    public function toFullJson()
    {
        $json = parent::toJson();

        return json_decode($json, true);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'public_id');
    }

    public function respondents()
    {
        return $this->hasMany(Respondent::class);
    }

    /**
     * funções extras
     */
    public function containsContentType(NotificationContentType $contentType): bool
    {
        if (empty($this->notifications_config)) return false;

        $collection = collect($this->notifications_config);

        return in_array(
            $contentType,
            $collection->pluck('contentType')->toArray()
        );
    }
}

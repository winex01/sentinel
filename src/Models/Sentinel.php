<?php

namespace Winex\Sentinel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Sentinel extends Model
{
    protected static function bootBelongsToUser(): void
    {
        // auto-fill user_id when creating
        static::creating(function ($model) {
            if (auth()->check() && empty($model->user_id)) {
                $model->user_id = auth()->id();
            }
        });

        // apply global scope
        static::addGlobalScope('user', function (Builder $query) {
            if (auth()->check()) {
                $query->where($query->getModel()->getTable().'.user_id', auth()->id());
            }
        });
    }

    public function user()
    {
        $userModel = config('auth.providers.users.model', \App\Models\User::class);
        return $this->belongsTo($userModel, 'user_id');
    }

    protected $guarded = [];

    protected $casts = [
        'expires_at' => 'date',
    ];
}

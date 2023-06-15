<?php

namespace App\Infrastructure\Eloquents;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent;
use Config;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public static function nameLike(Eloquent\Builder $builder, string $name)
    {
        $builder->where('name', 'like', "%{$name}%");
        return $builder;
    }

    public static function searchOrder(Eloquent\Builder $builder, string $column, string $destination)
    {
        $builder->orderBy($column, $destination);
        return $builder;
    }

    public static function executePaginate(Eloquent\Builder $builder)
    {
        $paginateLimit = Config::get('app.admin_paginate_limit', 30);
        return $builder->paginate($paginateLimit);
    }

}

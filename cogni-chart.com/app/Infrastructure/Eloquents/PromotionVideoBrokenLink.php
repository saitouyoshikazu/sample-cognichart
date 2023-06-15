<?php

namespace App\Infrastructure\Eloquents;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Config;

class PromotionVideoBrokenLink extends Model
{

    public $incrementing = false;

    protected $fillable = [
        'music_id',
    ];

    public function scopeMusicId(Builder $builder, string $music_id)
    {
        return $builder->where('music_id', $music_id);
    }

    public function scopeMusicIds(Builder $builder, array $music_ids)
    {
        return $builder->whereIn('music_id', $music_ids);
    }

    public function scopeSearchOrder(Builder $builder, string $column, string $destination)
    {
        return $builder->orderBy($column, $destination);
    }

    public function scopeExecutePaginate(Builder $builder)
    {
        $paginateLimit = Config::get('app.admin_paginate_limit', 30);
        return $builder->paginate($paginateLimit);
    }

}

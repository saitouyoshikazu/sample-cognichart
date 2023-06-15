<?php

namespace App\Infrastructure\Eloquents;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Config;

class Music extends Model
{

    public $incrementing = false;

    protected $fillable = [
        'id',
        'itunes_artist_id',
        'music_title',
        'itunes_base_url'
    ];

    public function scopeBusinessId(Builder $builder, string $itunes_artist_id, string $music_title)
    {
        return $builder->where('itunes_artist_id', $itunes_artist_id)->where('music_title', $music_title);
    }

    public function scopeExcludeId(Builder $builder, string $id)
    {
        return $builder->where('id', '!=', $id);
    }

    public function scopeCreatedAtGTE(Builder $builder, string $created_at)
    {
        return $builder->where('created_at', '>=', $created_at);
    }

    public function scopeCreatedAtLT(Builder $builder, string $created_at)
    {
        return $builder->where('created_at', '<', $created_at);
    }

    public function scopeMusicIdLike(Builder $builder, string $music_id)
    {
        return $builder->where('id', 'like', "{$music_id}%");
    }

    public function scopeITunesArtistId(Builder $builder, string $itunes_artist_id)
    {
        return $builder->where('itunes_artist_id', $itunes_artist_id);
    }

    public function scopeMusicTitleLike(Builder $builder, $music_title)
    {
        return $builder->where('music_title', 'like', "%{$music_title}%");
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

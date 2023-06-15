<?php

namespace App\Infrastructure\Eloquents;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Config;

class Artist extends Model
{
    public $incrementing = false;
    protected $fillable = [
        'id',
        'itunes_artist_id',
        'artist_name'
    ];

    public function scopeBusinessId(Builder $builder, string $itunes_artist_id)
    {
        return $builder->where('itunes_artist_id', $itunes_artist_id);
    }

    public function scopeExcludeId(Builder $builder, string $id)
    {
        return $builder->where('id', '!=', $id);
    }

    public function scopeITunesArtistId(Builder $builder, string $itunes_artist_id)
    {
        return $builder->where('itunes_artist_id', $itunes_artist_id);
    }

    public function scopeArtistNameLike(Builder $builder, string $artist_name)
    {
        return $builder->where('artist_name', 'like', "%{$artist_name}%");
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

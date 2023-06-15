<?php

namespace App\Infrastructure\Eloquents;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Config;

class ChartRankingItem extends Model
{

    public $incrementing = false;
    protected $fillable = [
        'id',
        'chart_artist',
        'chart_music',
        'artist_id',
        'music_id'
    ];

    public function scopeBusinessId(Builder $builder, string $chart_artist, string $chart_music)
    {
        return $builder->where('chart_artist', $chart_artist)->where('chart_music', $chart_music);
    }

    public function scopeExcludeId(Builder $builder, string $id)
    {
        return $builder->where('id', '!=', $id);
    }

    public function scopeSearchOrder(Builder $builder, string $column, string $destination)
    {
        return $builder->orderBy($column, $destination);
    }

    public function scopeChartArtistLike(Builder $builder, string $chart_artist)
    {
        return $builder->where('chart_artist', 'like', "%{$chart_artist}%");
    }

    public function scopeChartMusicLike(Builder $builder, string $chart_music)
    {
        return $builder->where('chart_music', 'like', "%{$chart_music}%");
    }

    public function scopeArtistId(Builder $builder, string $artist_id)
    {
        return $builder->where('artist_id', $artist_id);
    }

    public function scopeMusicId(Builder $builder, string $music_id)
    {
        return $builder->where('music_id', $music_id);
    }

    public function scopeNotAttached(Builder $builder)
    {
        return $builder
            ->where(
                function ($query) {
                    $query->whereNull('artist_id')->orWhere('artist_id', '');
                }
            )
            ->orWhere(
                function ($query) {
                    $query->whereNull('music_id')->orWhere('music_id', '');
                }
            );
    }

    public function scopeExecutePaginate(Builder $builder)
    {
        $paginateLimit = Config::get('app.admin_paginate_limit', 30);
        return $builder->paginate($paginateLimit);
    }

}

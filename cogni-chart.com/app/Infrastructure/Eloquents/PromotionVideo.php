<?php

namespace App\Infrastructure\Eloquents;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PromotionVideo extends Model
{

    public $incrementing = false;

    protected $fillable = [
        'music_id',
        'url',
        'thumbnail_url'
    ];

    public function scopeMusicId(Builder $builder, string $music_id)
    {
        return $builder->where('music_id', $music_id);
    }

}

<?php

namespace App\Infrastructure\Eloquents;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Chart extends Model
{

    public $incrementing = false;
    protected $fillable = [
        'id',
        'country_id',
        'display_position',
        'chart_name',
        'scheme',
        'host',
        'uri',
        'original_chart_name',
        'page_title'
    ];

    public function scopeBusinessId(Builder $builder, string $country_id, string $chart_name)
    {
        return $builder->where('country_id', $country_id)->where('chart_name', $chart_name);
    }

    public function scopeExcludeId(Builder $builder, string $id)
    {
        return $builder->where('id', '!=', $id);
    }

}

<?php

namespace App\Infrastructure\Eloquents;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ChartTerm extends Model
{

    public $incrementing = false;
    protected $fillable = [
        'id',
        'chart_id',
        'start_date',
        'end_date'
    ];

    public function scopeBusinessId(Builder $builder, string $chart_id, string $end_date)
    {
        return $builder->where('chart_id', $chart_id)->where('end_date', $end_date);
    }

    public function scopeExcludeId(Builder $builder, string $id)
    {
        return $builder->where('id', '!=', $id);
    }

}

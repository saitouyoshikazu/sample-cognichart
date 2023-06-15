<?php

namespace App\Infrastructure\Eloquents;

use Illuminate\Database\Eloquent\Model;

class ChartRanking extends Model
{

    public $incrementing = false;
    protected $fillable = [
        'chart_term_id',
        'ranking',
        'chart_ranking_item_id'
    ];
    protected $primaryKey = null;

}

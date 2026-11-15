<?php

namespace Viodev;

use Illuminate\Database\Eloquent\Builder;

trait Pageable
{
    public function scopePage(Builder $query, Pager $pager): Builder
    {
        if(!is_null($pager->lastId)){
            $query->where('id', $pager->order == "asc" ? ">" : "<", $pager->lastId);
        }

        $query->orderBy('id', $pager->order);
        $query->limit($pager->limit);

        return $query;
    }
}

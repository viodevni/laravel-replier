<?php

namespace Viodev;

trait Pageable
{
    public function scopePage($query, Pager $pager)
    {
        if(!is_null($pager->last_id)){
            $query->where('id', $pager->order == "asc" ? ">" : "<", $pager->last_id);
        }

        $query->orderBy('id', $pager->order);
        $query->limit($pager->limit);

        return $query;
    }
}
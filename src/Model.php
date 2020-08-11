<?php

namespace Viodev;

class Model extends \Illuminate\Database\Eloquent\Model
{
    public static function boot()
    {
        parent::boot();

        // Do something
    }

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
<?php

namespace Viodev;

class Fractal
{
    public static function item($data, $transformer, Sparser $sparser = null) : array
    {
        return (new $transformer)->create($data, $sparser);
    }

    public static function collection($data, $transformer, Pager $pager, Sparser $sparser = null) : array
    {
        $collection = [];

        foreach($data as $item){
            $collection[] = self::item($item, $transformer, $sparser);
        }

        return [
            'meta' => [
                'count' => count($collection),
                'limit' => $pager->limit,
                'order' => $pager->order,
                'last_id' => self::lastId($data)
            ],
            'data' => $collection
        ];
    }

    private static function lastId($data)
    {
        if ($data instanceof \Illuminate\Support\Collection) return $data->last()->id;
        if (is_array($data)) {
            $last = end($data);
            if (isset($last['id'])) return $last['id'];
        }
        return null;
    }
}
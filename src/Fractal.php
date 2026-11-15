<?php

namespace Viodev;

use Illuminate\Support\Collection;

class Fractal
{
    public static function item(mixed $data, string $transformer, ?Sparser $sparser = null) : array
    {
        return (new $transformer)->create($data, $sparser);
    }

    public static function collection(iterable $data, string $transformer, Pager $pager, ?Sparser $sparser = null) : array
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

    private static function lastId(iterable $data): ?int
    {
        $lastId = null;

        if ($data instanceof Collection) {
            $lastId = $data->last()->id ?? null;
        } elseif (is_array($data)) {
            $last = end($data);
            $lastId = $last['id'] ?? null;
        }

        return $lastId === null ? null : (int) $lastId;
    }
}

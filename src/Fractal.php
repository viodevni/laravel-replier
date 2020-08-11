<?php

namespace Viodev;

use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Collection;
use League\Fractal\Serializer\ArraySerializer;
use League\Fractal\Serializer\DataArraySerializer;

class Fractal
{
    public static function item($data, $transformer)
    {
        $manager = new Manager();
        $manager->setSerializer(new DataArraySerializer());

        $resource = new Item($data, new $transformer());

        return $manager->createData($resource)->toArray();
    }

    public static function collection($data, string $transformer, Pager $pager)
    {
        $manager = new Manager();
        $manager->setSerializer(new ArraySerializer());

        $resource = new Collection($data, new $transformer());

        $resource->setMeta([
            'count' => $data->count(),
            'limit' => $pager->limit,
            'order' => $pager->order,
            'last_id' => $data->last()->id ?? null
        ]);

        return $manager->createData($resource)->toArray();
    }
}
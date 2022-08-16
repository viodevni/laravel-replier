<?php

namespace Viodev\Transformers;

use Viodev\Sparser;

/**
 * @method transform($data)
 */

class Transformer
{
    public function create($data, ?Sparser $sparser) : array
    {
        $transformed = $this->transform($data);

        if(!is_null($sparser) && !empty($sparser->fields)) return array_intersect_key($transformed, array_flip($sparser->fields));

        return $transformed;
    }
}
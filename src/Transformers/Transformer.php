<?php

namespace Viodev\Transformers;

use Viodev\Sparser;

class Transformer
{
    public function create($data, ?Sparser $sparser)
    {
        $transformed = $this->transform($data);

        return is_null($sparser) ? $transformed : array_intersect_key($transformed, array_flip($sparser->fields));
    }
}
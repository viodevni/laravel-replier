<?php

use Viodev\Replier;

if (!function_exists("reply")) {
    /**
     * Reply factory
     *
     * @return Replier
     */
    function reply(): Replier
    {
        return app(Replier::class);
    }
}
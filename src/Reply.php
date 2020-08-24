<?php

namespace Viodev;

use Illuminate\Support\Facades\Facade;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @method static JsonResponse success()
 * @method static JsonResponse fail(...$args)
 * @method static JsonResponse error(...$args)
 * @method static JsonResponse multi(array $results)
 * @method static JsonResponse unauthorized()
 * @method static JsonResponse forbidden()
 * @method static JsonResponse notFound()
 * @method static JsonResponse methodNotAllowed()
 * @method static JsonResponse maintenanceMode()
 * @method static JsonResponse validationFailed()
 * @method static JsonResponse tooManyRequests()
 * @method static Replier withStatus(int $status)
 * @method static Replier withData(array $data)
 *
 * @see ReplyFactory
 */

class Reply extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Replier::class;
    }
}
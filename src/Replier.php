<?php

namespace Viodev;

use Illuminate\Contracts\Translation\Translator;
use Symfony\Component\HttpFoundation\JsonResponse;

class Replier
{
    private const SUCCESS = "success";
    private const FAIL = "fail";
    private const ERROR = "error";
    private const MULTI = "multi";

    private $translator, $lang_prefix;

    public $result = null;
    public $status = null;
    public $data = array();

    public function __construct(Translator $translator, $lang_prefix)
    {
        $this->translator = $translator;
        $this->lang_prefix = $lang_prefix;
    }

    public function reply()
    {
        $response = new JsonResponse();
        $response->setStatusCode($this->status);

        $body['result'] = $this->result;
        $body['status'] = $this->status;

        if(isset($this->code)) $body['code'] = $this->code;
        if(isset($this->message)) $body['message'] = $this->message;
        if(isset($this->meta)) $body['meta'] = $this->meta;

        $body['data'] = $this->data;

        $response->setData($body);

        return $response;
    }

    public function resolveCode($args)
    {
        if(count($args) == 0) return;

        if(!is_string($args[0])) return;

        $this->code = $args[0];

        $args[0] = $this->lang_prefix . $args[0];

        if(isset($args[1]) && is_int($args[1])){
            $this->message = call_user_func_array([$this->translator, "choice"], $args);
        } else {
            $this->message = call_user_func_array([$this->translator, "get"], $args);
        }
    }

    public function success()
    {
        $this->result = self::SUCCESS;
        $this->status = $this->status ?? 200;
        return $this->reply();
    }

    public function fail(...$args)
    {
        $this->result = self::FAIL;
        $this->status = $this->status ?? 422;
        $this->resolveCode($args);

        return $this->reply();
    }

    public function error(...$args)
    {
        $this->result = self::ERROR;
        $this->status = $this->status ?? 500;
        $this->resolveCode($args);

        return $this->reply();
    }

    public function multi(array $results)
    {
        $this->result = self::MULTI;
        $this->status = 207;

        foreach($results as $result){
            $this->data[] = (array) $result;
        }

        return $this->reply();
    }

    public function unauthorized()
    {
        return $this->withStatus(401)->fail('unauthorized');
    }

    public function forbidden()
    {
        return $this->withStatus(403)->fail('forbidden');
    }

    public function notFound()
    {
        return $this->withStatus(404)->fail('not_found');
    }

    public function methodNotAllowed()
    {
        return $this->withStatus(405)->fail('method_not_allowed');
    }

    public function maintenanceMode()
    {
        return $this->withStatus(405)->fail('maintenance');
    }

    public function validationFailed()
    {
        return $this->withStatus(422)->fail('validation_failed');
    }

    public function tooManyRequests()
    {
        return $this->withStatus(429)->fail('too_many_requests');
    }

    public function withStatus(int $status)
    {
        $this->status = $status;
        return $this;
    }

    public function withData(array $data)
    {
        $this->data = $data['data'] ?? $data;
        if(isset($data['meta'])) $this->meta = $data['meta'];
        return $this;
    }
}
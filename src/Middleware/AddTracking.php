<?php

namespace Haruncpi\LaravelUserActivity\Middleware;

use Closure;
use Log;
use Mexion\BedrockCore\DataObjects\Security\Tarpit\TarpitTypeHackAttempt;
use Mexion\BedrockCore\Observers\Events\TarpitTriggerEvent;
use Haruncpi\LaravelUserActivity\Events\RequestTerminatedEvent;

class AddTracking
{
    protected $requestId;

    protected $startTime;

    public function handle($request, Closure $next)
    {
        $this->startTime = microtime(true);
        $this->requestId = $this->generateKey();

        $request->attributes->add(["request_id" => $this->requestId]);
        $request->attributes->add(["request_start" => $this->startTime]);

        return $next($request);
    }

    private function generateKey()
    {
        return md5(uniqid(mt_rand(), true));
    }

    public function terminate($request, $response)
    {
        event(new RequestTerminatedEvent($request, $response));
    }
}




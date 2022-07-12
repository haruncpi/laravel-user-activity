<?php

namespace Haruncpi\LaravelUserActivity\Listeners\Traits;

trait LoggingTrait
{
    private function cleanPayload(string $payload)
    {
        if (!config('user-activity.log_response.enabled')) {
            return 'nope';
        }

        $truncated = substr($payload, 0,config('user-activity.log_response.max_characters'));

        return base64_encode($truncated);  // base64 encode to prevent sqli
    }
}

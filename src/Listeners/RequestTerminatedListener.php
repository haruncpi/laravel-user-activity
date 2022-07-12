<?php

namespace Haruncpi\LaravelUserActivity\Listeners;

use Haruncpi\LaravelUserActivity\Events\RequestTerminatedEvent;
use Haruncpi\LaravelUserActivity\Listeners\Traits\LoggingTrait;
use Haruncpi\LaravelUserActivity\Models\Log;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Events\Routing;

class RequestTerminatedListener
{
    use LoggingTrait;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function handle(RequestTerminatedEvent $event)
    {
        $request = $event->getRequest();

        $requestId = $request->get('request_id');
        $requestStart = $request->get('request_start');

        $duration = null;
        if ($requestStart > 0) {
            $duration = microtime(true) - $requestStart;
        }

        $payload = $event->getResponse()->getContent();
        $payload = $this->cleanPayload($payload);

        $table = (new Log())->getTable();
        $statement = "UPDATE $table 
            SET {$table}.request_duration = $duration,
                {$table}.payload_base64 = '$payload'
            WHERE {$table}.request_id = '$requestId'";

        DB::statement($statement);
    }
}
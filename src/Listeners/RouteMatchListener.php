<?php

namespace Haruncpi\LaravelUserActivity\Listeners;

use Haruncpi\LaravelUserActivity\Listeners\Traits\LoggingTrait;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Events\RouteMatched;

class RouteMatchListener
{
    use LoggingTrait;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function handle(RouteMatched $event)
    {
        if (!config('user-activity.log_events.on_route')) {
            return;
        }

        $url = $event->request->fullUrl();
        $ignoreUrls = config('user-activity.ignore_urls');
        if ($this->shouldIgnore($url, $ignoreUrls)) {
            return;
        }

        $route = '';
        if (array_key_exists('as', $event->route->action)) {
            $route = $event->route->action['as'];

            $ignoreRoutes = config('user-activity.ignore_routes');
            if ($this->shouldIgnore($route, $ignoreRoutes)) {
                return;
            }
        }



        $requestId = $event->request->get('request_id');
        $requestStart = $event->request->get('request_start');
        $methods = implode(',', $event->route->methods);

        $payload = $event->request->getContent();
        $payload = $this->cleanPayload($payload);


        $user = Auth::user();
        $dateTime = date('Y-m-d H:i:s');

        $data = [
            'methods' => $methods,
        ];

        DB::table('logs')->insert([
            'route' => $route,
            'url' => $url,
            'ip'         => $this->request->ip(),
            'request_id' => $requestId,
            'request_start' => $requestStart,
            'payload_base64' => $payload,
            'user_agent' => $this->request->userAgent(),
            'user_id'    => $user ? $user->id : null,
            'log_date'   => $dateTime,
            'table_name' => '',
            'log_type'   => 'route',
            'data'       => json_encode($data)
        ]);
    }

    private function shouldIgnore(string $route, array $ignoreRoutes)
    {
        foreach ($ignoreRoutes as $ignorePattern) {
            if (fnmatch($ignorePattern, $route)) {
                return true;
            }
        }

        return false;
    }
}
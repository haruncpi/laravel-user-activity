<?php

namespace Haruncpi\LaravelUserActivity\Listeners;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LockoutListener
{
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function handle($event)
    {
        if (!config('user-activity.log_events.on_lockout', false)) return;

        if (!$event->request->has('email')) return;
        $user = User::where('email', $event->request->input('email'))->first();
        if (!$user) return;


        $data = [
            'ip'         => $this->request->ip(),
            'user_agent' => $this->request->userAgent()
        ];

        DB::table('logs')->insert([
            'user_id'    => $user->id,
            'log_date'   => date('Y-m-d H:i:s'),
            'table_name' => '',
            'log_type'   => 'lockout',
            'data'       => json_encode($data)
        ]);

    }
}
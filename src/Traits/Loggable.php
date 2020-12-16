<?php

namespace Haruncpi\LaravelUserActivity\Traits;

use Illuminate\Support\Facades\DB;

trait Loggable
{
    static protected $logTable = 'logs';

    static function logToDb($model, $logType)
    {
       $guarded =  config('user-activity.admin_guard', 'user');
        if (!auth()->guard($guarded)->check()) return;
        $originalData = json_encode($model->getOriginal());

        $tableName = $model->getTable();
        $dateTime = date('Y-m-d H:i:s');
        $userId = auth()->guard($guarded)->user()->id;

        DB::table(self::$logTable)->insert([
            'user_id'    => $userId,
            'log_date'   => $dateTime,
            'table_name' => $tableName,
            'log_type'   => $logType,
            'data'       => $originalData
        ]);
    }

    public static function bootLoggable()
    {
        if (config('user-activity.log_events.on_edit', false)) {
            self::updated(function ($model) {
                self::logToDb($model, 'edit');
            });
        }


        if (config('user-activity.log_events.on_delete', false)) {
            self::deleted(function ($model) {
                self::logToDb($model, 'delete');
            });
        }
    }
}

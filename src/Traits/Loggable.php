<?php

namespace Haruncpi\LaravelUserActivity\Traits;

use Illuminate\Support\Facades\DB;

trait Loggable
{
    static protected $logTable = 'logs';

    static function logToDb($model, $logType)
    {
        if (!auth()->check() || $model->excludeLogging || !config('user-activity.activated', true)) return;
        if ($logType == 'create') $originalData = json_encode($model);
        else {
            if (version_compare(app()->version(), '7.0.0', '>='))
                $originalData = json_encode($model->getRawOriginal()); // getRawOriginal available from Laravel 7.x
            else
                $originalData = json_encode($model->getOriginal());
        }

        $tableName = $model->getTable();
        $dateTime = date('Y-m-d H:i:s');
        $userId = auth()->user()->id;

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


        if (config('user-activity.log_events.on_create', false)) {
            self::created(function ($model) {
                self::logToDb($model, 'create');
            });
        }
    }
}

<?php namespace Haruncpi\LaravelUserActivity\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
class Log extends Model
{
    public $timestamps = false;
    public $dates = ['log_date'];
    protected $appends = ['dateHumanize','json_data'];
    private $model;
    protected $config;


    public function getDateHumanizeAttribute()
    {
        $this->model = config('user-activity.model');
        return $this->log_date->diffForHumans();
    }

    public function getJsonDataAttribute()
    {
        return json_decode($this->data,true);
    }

    public function user()
    {
        $model = config('user-activity.model');
        return $this->belongsTo($model);
    }
}

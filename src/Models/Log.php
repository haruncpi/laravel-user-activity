<?php namespace Haruncpi\LaravelUserActivity\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    public $timestamps = false;
    public $dates = ['log_date'];
    protected $appends = ['dateHumanize','json_data'];

    public function getDateHumanizeAttribute()
    {
        return $this->log_date->diffForHumans();
    }

    public function getJsonDataAttribute()
    {
        return json_decode($this->data,true);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

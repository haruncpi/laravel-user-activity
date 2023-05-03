<?php namespace Haruncpi\LaravelUserActivity\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    /**
     * @var bool
     */
    public $timestamps = false;
    /**
     * @var array|string[]
     */
    public $dates = ['log_date'];
    /**
     * @var array|string[]
     */
    protected $appends = ['dateHumanize', 'json_data'];

    /**
     * @var string
     */
    private $userInstance = "\App\Models\User";


    /**
     *
     */
    public function __construct()
    {
        $userInstance = config('user-activity.model.user');
        if (!empty($userInstance)) $this->userInstance = $userInstance;
    }

    /**
     * @return mixed
     */
    public function getDateHumanizeAttribute()
    {
        return Carbon::parse($this->log_date)->diffForHumans();
        //return $this->log_date->diffForHumans();
    }

    /**
     * @return mixed
     */
    public function getJsonDataAttribute()
    {
        return json_decode($this->data, true);
    }

    /**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo($this->userInstance);
    }
}

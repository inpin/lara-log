<?php

namespace Inpin\LaraLog;

use Carbon\Carbon;
use Illuminate\Foundation\Auth\User;
use Jenssegers\Mongodb\Eloquent\Model;

/**
 * Class ApiActionLog.
 *
 * @property int id
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property int user_id
 * @property string route_name
 * @property string uri
 * @property string method
 * @property string body
 * @property User user
 */
class ApiActionLog extends Model
{
    protected $collection = 'lara_log_api_action_logs';
    protected $connection = 'mongodb';
    public $timestamps = true;
    protected $fillable = [
        'user_id',
        'route_name',
        'uri',
        'method',
        'body',
    ];

    public function user()
    {
        return $this->belongsTo(
            'Illuminate\Foundation\Auth\User',
            'user_id',
            'id'
        );
    }
}

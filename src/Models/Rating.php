<?php
namespace  SaliBhdr\TyphoonRate\Models;

use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $fillable = ['score','user_id'];

    /**
     * @return mixed
     */
    public function rateable()
    {
        return $this->morphTo();
    }

    /**
     * Rating belongs to a user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        $userClassName = Config::get('auth.model');
        if (is_null($userClassName)) {
            $userClassName = Config::get('auth.providers.users.model');
        }

        return $this->belongsTo($userClassName);
    }
}

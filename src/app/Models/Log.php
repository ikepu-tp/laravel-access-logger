<?php

namespace ikepu_tp\AccessLogger\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ["id", "created_at", "updated_at"];

    public function log_info()
    {
        return $this->hasOne(Log_info::class);
    }

    public function log_request()
    {
        return $this->hasOne(Log_request::class);
    }

    public function log_heads()
    {
        return $this->hasMany(Log_head::class);
    }

    public function log_servers()
    {
        return $this->hasMany(Log_server::class);
    }

    public function log_response()
    {
        return $this->hasOne(Log_response::class);
    }
}

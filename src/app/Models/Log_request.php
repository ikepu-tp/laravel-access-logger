<?php

namespace ikepu_tp\AccessLogger\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log_request extends Model
{
    use HasFactory;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'queries' => 'encrypted:array',
        'bodies' => 'encrypted:array',
    ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ["id", "created_at", "updated_at"];

    public function log()
    {
        return $this->belongsTo(Log::class, 'log_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QueueTicket extends Model
{
    protected $table = 'queue_tickets';

    protected $fillable = [
        'name',
        'tracking_number',
        'device_id',
        'platform',
        'mobile_number',
        'status',
        'assigned_teller',
        'queue_date',
    ];

    public const STATUS_HOLDING = 'holding';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_SERVING = 'serving';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_NOSHOW = 'no-show';

    public function scopeHolding($query)
    {
        return $query->where('status', self::STATUS_HOLDING)
                     ->where('queue_date', today()->toDateString())
                     ->orderBy('created_at', 'asc');
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)
                     ->where('queue_date', today()->toDateString())
                     ->orderBy('created_at', 'asc');
    }

    public function scopeServing($query)
    {
        return $query->where('status', self::STATUS_SERVING)
                     ->where('queue_date', today()->toDateString());
    }
}

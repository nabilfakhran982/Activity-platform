<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = [
        'activity_id', 'day_of_week',
        'start_time', 'end_time', 'is_active'
    ];

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}

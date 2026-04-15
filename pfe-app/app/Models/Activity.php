<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = [
        'center_id',
        'category_id',
        'title',
        'description',
        'min_age',
        'max_age',
        'price',
        'capacity',
        'level',
        'is_private',
        'is_active'
    ];

    public function center()
    {
        return $this->belongsTo(Center::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function images()
    {
        return $this->hasMany(ActivityImage::class);
    }

    public function favourites()
    {
        return $this->hasMany(Favourite::class);
    }

    public function getBgClass(): string
    {
        return [
            'Martial Arts' => 'bg-martial',
            'Boxing' => 'bg-boxing',
            'Arts & Crafts' => 'bg-arts',
            'Swimming' => 'bg-swimming',
            'Pilates' => 'bg-pilates',
            'Fitness & Gym' => 'bg-fitness',
            'Adventure & Outdoor' => 'bg-outdoor',
            'Football' => 'bg-football',
        ][$this->category->name] ?? 'bg-default';
    }

    public function getAvgRatingAttribute()
    {
        return $this->reviews->avg('rating');
    }
}

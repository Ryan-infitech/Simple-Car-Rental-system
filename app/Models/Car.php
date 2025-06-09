<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use HasFactory;

    protected $fillable = [
        'brand',
        'model',
        'year',
        'license_plate',
        'color',
        'transmission',
        'fuel_type',
        'seats',
        'price_per_day',
        'status',
        'description',
        'image_path'
    ];

    protected $casts = [
        'price_per_day' => 'decimal:2',
        'year' => 'integer',
        'seats' => 'integer',
    ];

    // Relationships
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function images()
    {
        return $this->hasMany(CarImage::class);
    }

    // Helper methods
    public function isAvailable()
    {
        return $this->status === 'available';
    }

    public function isRented()
    {
        return $this->status === 'rented';
    }

    public function inMaintenance()
    {
        return $this->status === 'maintenance';
    }
}

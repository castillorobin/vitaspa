<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'user_id',
        'appointment_date',
        'appointment_time',
        'duration_minutes',
        'service',
        'status',
        'price',
        'payment_method',
        'notes',
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'price' => 'decimal:2',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function attendedBy()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
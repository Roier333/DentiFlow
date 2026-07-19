<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'dentist_id',
        'created_by_user_id',
        'date',
        'time',
        'end_time',
        'tipo',
        'estado',
        'color',
        'descuento',
        'procedimiento_extra',
        'costo_extra',
        'completion_description',
        'cancellation_reason',
        'reminder_sent',
        'notas',
    ];

    protected $casts = [
        'date' => 'date',
        'descuento' => 'decimal:2',
        'costo_extra' => 'decimal:2',
        'reminder_sent' => 'boolean',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function dentist(): BelongsTo
    {
        return $this->belongsTo(Dentist::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function treatments(): HasMany
    {
        return $this->hasMany(Treatment::class);
    }
}

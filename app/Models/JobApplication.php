<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'vacancy_id',
        'full_name',
        'email',
        'phone_number',
        'file',
        'application_note',
        'application_type',
        'interested_position',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function vacancy(): BelongsTo
    {
        return $this->belongsTo(Vacancy::class, 'vacancy_id');
    }
}

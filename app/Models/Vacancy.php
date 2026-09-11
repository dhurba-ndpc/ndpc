<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vacancy extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title_en',
        'title_ne',
        'slug',
        'location',
        'employment_type',
        'short_description_en',
        'short_description_ne',
        'description_en',
        'description_ne',
        'salary',
        'experience_level',
        'total_applicants',
        'deadline',
        'external_link',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'total_applicants' => 'integer',
        'deadline' => 'datetime',
    ];

    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class, 'vacancy_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecruitmentResult extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title_en',
        'title_ne',
        'selected_candidates',
        'waiting_candidates',
        'is_active',
    ];

    protected $casts = [
        'selected_candidates' => 'array',
        'waiting_candidates' => 'array',
        'is_active' => 'boolean',
    ];
}

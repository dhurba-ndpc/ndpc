<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Person extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'people';

    protected $fillable = [
        'type',
        'name_en',
        'name_ne',
        'designation_en',
        'designation_ne',
        'organization_en',
        'organization_ne',
        'description_en',
        'description_ne',
        'image',
        'sort_order',
        'meta_json',
        'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'meta_json' => 'array',
        'is_active' => 'boolean',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostTranslation extends Model
{
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = ['post_id', 'locale'];

    protected $fillable = [
        'post_id',
        'locale',
        'title',
        'badge_title',
        'subtitle',
        'short_description',
        'description',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'post_id');
    }
}

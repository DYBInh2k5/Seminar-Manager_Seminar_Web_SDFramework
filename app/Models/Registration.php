<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Registration extends Model
{
    protected $fillable = [
        'topic_id',
        'student_id',
        'status',
    ];

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function presentation(): HasOne
    {
        return $this->hasOne(Presentation::class);
    }

    public function score(): HasOne
    {
        return $this->hasOne(Score::class);
    }

    public function submission(): HasOne
    {
        return $this->hasOne(Submission::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdditionalSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'mentee_id',
        'mentoring_group_id',
        'topic',
        'date',
        'status',
        'proof_path',
    ];

    /**
     * Get the mentee that owns the session.
     */
    public function mentee()
    {
        return $this->belongsTo(User::class, 'mentee_id');
    }

    /**
     * Get the mentoring group for the session.
     */
    public function mentoringGroup()
    {
        return $this->belongsTo(MentoringGroup::class);
    }
}
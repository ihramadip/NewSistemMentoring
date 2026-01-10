<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubmissionAnswer extends Model
{
    protected $fillable = [
        'exam_submission_id',
        'question_id',
        'chosen_option_id',
        'answer_text',
        'score',
    ];

    public function examSubmission()
    {
        return $this->belongsTo(ExamSubmission::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function chosenOption()
    {
        return $this->belongsTo(Option::class, 'chosen_option_id');
    }
}

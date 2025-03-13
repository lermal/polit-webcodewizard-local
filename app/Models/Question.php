<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'test_id',
        'question_text',
        'options',
        'correct_answers',
        'points',
        'is_multiple'
    ];

    protected $casts = [
        'options' => 'array',
        'correct_answers' => 'array',
        'is_multiple' => 'boolean'
    ];

    public function test()
    {
        return $this->belongsTo(Test::class);
    }

    public function isCorrectAnswer($answers)
    {
        if (!is_array($answers)) {
            $answers = [$answers];
        }

        sort($answers);
        $correctAnswers = $this->correct_answers;
        sort($correctAnswers);

        return $answers == $correctAnswers;
    }
}

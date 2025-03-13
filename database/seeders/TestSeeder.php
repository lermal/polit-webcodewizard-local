<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Test;
use App\Models\Question;

class TestSeeder extends Seeder
{
    public function run()
    {
        $test = Test::create([
            'title' => 'Демонстрационный тест',
            'description' => 'Это демонстрационный тест для проверки функционала',
            'is_active' => true
        ]);

        $questions = [
            [
                'question_text' => 'Какой язык программирования мы используем?',
                'options' => ['PHP', 'Python', 'JavaScript', 'Java'],
                'correct_answers' => ['PHP'],
                'points' => 1,
                'is_multiple' => false
            ],
            [
                'question_text' => 'Выберите фреймворки PHP',
                'options' => ['Laravel', 'Django', 'Express', 'Symfony'],
                'correct_answers' => ['Laravel', 'Symfony'],
                'points' => 2,
                'is_multiple' => true
            ]
        ];

        foreach ($questions as $q) {
            $test->questions()->create($q);
        }
    }
}

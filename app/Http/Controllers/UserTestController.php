<?php

namespace App\Http\Controllers;

use App\Models\Test;
use App\Models\Question;
use Illuminate\Http\Request;

class UserTestController extends Controller
{
    public function index()
    {
        $tests = Test::where('is_active', true)
            ->withCount('questions')
            ->get();

        // Добавим отладочную информацию
        if (config('app.debug')) {
            \Log::info('Tests query:', [
                'count' => $tests->count(),
                'sql' => Test::where('is_active', true)->withCount('questions')->toSql(),
                'tests' => $tests->toArray()
            ]);
        }

        return view('user.tests.index', compact('tests'));
    }

    public function show(Test $test)
    {
        if (!$test->is_active) {
            return redirect()->route('user.tests.index')
                ->with('error', 'Тест недоступен');
        }

        return view('user.tests.show', compact('test'));
    }

    public function submit(Request $request, Test $test)
    {
        $validated = $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'required'
        ]);

        $totalPoints = 0;
        $correctAnswers = 0;
        $results = [];

        foreach ($test->questions as $question) {
            $userAnswers = (array) ($validated['answers'][$question->id] ?? []);
            $isCorrect = $question->isCorrectAnswer($userAnswers);

            $results[$question->id] = [
                'is_correct' => $isCorrect,
                'points' => $isCorrect ? $question->points : 0,
                'user_answers' => $userAnswers,
                'correct_answers' => $question->correct_answers
            ];

            if ($isCorrect) {
                $totalPoints += $question->points;
                $correctAnswers++;
            }
        }

        $totalQuestions = $test->questions->count();
        $percentageCorrect = ($correctAnswers / $totalQuestions) * 100;

        // Сохраняем результат теста
        $testResult = auth()->user()->testResults()->create([
            'test_id' => $test->id,
            'score' => $totalPoints,
            'percentage' => round($percentageCorrect, 2),
            'answers' => $results
        ]);

        return response()->json([
            'total_points' => $totalPoints,
            'correct_answers' => $correctAnswers,
            'total_questions' => $totalQuestions,
            'percentage' => round($percentageCorrect, 2),
            'results' => $results
        ]);
    }

    public function rate(Request $request, Test $test)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5'
        ]);

        auth()->user()->testRatings()->updateOrCreate(
            ['test_id' => $test->id],
            ['rating' => $validated['rating']]
        );

        return response()->json(['message' => 'Оценка сохранена']);
    }
}

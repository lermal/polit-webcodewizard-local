<?php

namespace App\Http\Controllers;

use App\Models\Test;
use App\Models\Question;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index()
    {
        $tests = Test::with('questions')->get();
        return view('admin.tests.index', compact('tests'));
    }

    public function create()
    {
        return view('admin.tests.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'questions' => 'required|array|min:1',
                'questions.*.question_text' => 'required|string',
                'questions.*.options' => 'required|array|min:2',
                'questions.*.correct_answers' => 'required|array|min:1',
                'questions.*.is_multiple' => 'required|boolean',
                'questions.*.points' => 'required|integer|min:1'
            ]);

            $test = Test::create([
                'title' => $validated['title'],
                'description' => $validated['description']
            ]);

            foreach ($validated['questions'] as $questionData) {
                // Проверяем, что все правильные ответы существуют в опциях
                $options = $questionData['options'];
                $correctAnswers = $questionData['correct_answers'];

                if (!empty(array_diff($correctAnswers, $options))) {
                    return response()->json(['message' => 'Правильные ответы должны быть из списка вариантов'], 422);
                }

                $test->questions()->create($questionData);
            }

            return response()->json([
                'message' => 'Тест успешно создан',
                'redirect' => route('admin.tests.index')
            ]);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Ошибка при создании теста: ' . $e->getMessage()], 422);
        }
    }

    public function edit(Test $test)
    {
        if (request()->expectsJson()) {
            return response()->json($test->load('questions'));
        }
        return view('admin.tests.edit', compact('test'));
    }

    public function update(Request $request, Test $test)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'questions' => 'required|array|min:1',
                'questions.*.question_text' => 'required|string',
                'questions.*.options' => 'required|array|min:2',
                'questions.*.correct_answers' => 'required|array|min:1',
                'questions.*.is_multiple' => 'required|boolean',
                'questions.*.points' => 'required|integer|min:1'
            ]);

            $test->update([
                'title' => $validated['title'],
                'description' => $validated['description']
            ]);

            $test->questions()->delete();

            foreach ($validated['questions'] as $questionData) {
                // Проверяем, что все правильные ответы существуют в опциях
                $options = $questionData['options'];
                $correctAnswers = $questionData['correct_answers'];

                if (!empty(array_diff($correctAnswers, $options))) {
                    return response()->json(['message' => 'Правильные ответы должны быть из списка вариантов'], 422);
                }

                $test->questions()->create($questionData);
            }

            return response()->json([
                'message' => 'Тест успешно обновлен',
                'redirect' => route('admin.tests.index')
            ]);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Ошибка при обновлении теста: ' . $e->getMessage()], 422);
        }
    }

    public function destroy(Test $test)
    {
        try {
            $test->delete();
            return response()->json([
                'message' => 'Тест успешно удален'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ошибка при удалении теста: ' . $e->getMessage()
            ], 422);
        }
    }
}

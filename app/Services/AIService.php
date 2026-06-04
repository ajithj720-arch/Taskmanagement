<?php

namespace App\Services;

use App\Models\Task;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{
    public function generateSummary(Task $task): array
    {
        if (config('app.ai_mock', true)) {
            return $this->mockResponse($task);
        }

        try {
            return $this->callOpenAI($task);
        } catch (\Throwable $e) {
            Log::error('AIService failed', ['error' => $e->getMessage()]);
            return $this->mockResponse($task);
        }
    }

    private function callOpenAI(Task $task): array
    {
        $prompt = $this->buildPrompt($task);

        $response = Http::withToken(config('services.openai.key'))
            ->timeout(15)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a task management assistant. Respond only with valid JSON.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.3,
            ]);

        $data = $response->json('choices.0.message.content');
        $parsed = json_decode($data, true);

        return [
            'ai_summary' => $parsed['summary'] ?? 'Unable to generate summary.',
            'ai_priority' => in_array($parsed['priority'] ?? '', ['low', 'medium', 'high'])
                ? $parsed['priority']
                : $task->priority->value,
        ];
    }

    private function buildPrompt(Task $task): string
    {
        return <<<PROMPT
Analyze this task and provide a brief summary and suggested priority.

Task Title: {$task->title}
Description: {$task->description}
Current Priority: {$task->priority->value}
Status: {$task->status->value}
Due Date: {$task->due_date?->toDateString()}

Respond with JSON: {"summary": "2-3 sentence summary", "priority": "low|medium|high"}
PROMPT;
    }

    private function mockResponse(Task $task): array
    {
        $summaries = [
            "This task involves {$task->title}. It has been assessed and requires attention based on the provided description. Recommended for timely completion.",
            "Task '{$task->title}' has been analyzed. The scope appears well-defined and execution should follow standard procedures.",
            "Based on the task details, '{$task->title}' is a structured work item requiring focused effort to complete successfully.",
        ];

        return [
            'ai_summary' => $summaries[array_rand($summaries)],
            'ai_priority' => $task->priority->value,
        ];
    }
}

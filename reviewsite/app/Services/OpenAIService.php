<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class OpenAIService
{
    protected string $apiKey;
    protected string $endpoint;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key');
        $this->endpoint = config('services.openai.endpoint', 'https://api.openai.com/v1/chat/completions');
        $this->model = config('services.openai.model', 'gpt-3.5-turbo');
    }

    protected function callOpenAI(string $prompt, int $maxTokens = 256): string
    {
        if (!$this->apiKey) {
            throw new Exception('OpenAI API key is not configured.');
        }

        $client = Http::timeout(30);
        if (env('OPENAI_BYPASS_SSL', false)) {
            $client = $client->withOptions(['verify' => false]);
        }

        $response = $client
            ->withToken($this->apiKey)
            ->post($this->endpoint, [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a helpful assistant for a video game review site.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'max_tokens' => $maxTokens,
                'temperature' => 0.7,
            ]);

        if ($response->failed()) {
            Log::error('OpenAI API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new Exception('Failed to generate content with OpenAI.');
        }

        $data = $response->json();
        return $data['choices'][0]['message']['content'] ?? '';
    }

    public function generateDescription(string $gameName, string $genre = null): string
    {
        $prompt = "Write a compelling, concise description for the video game '$gameName'";
        if ($genre) {
            $prompt .= " (genre: $genre)";
        }
        $prompt .= ". Limit to 2-3 sentences.";
        return $this->callOpenAI($prompt, 200);
    }

    public function generateStory(string $gameName, string $genre = null): string
    {
        $prompt = "Write a detailed story or narrative summary for the video game '$gameName'";
        if ($genre) {
            $prompt .= " (genre: $genre)";
        }
        $prompt .= ". Include main characters, setting, and plot.";
        return $this->callOpenAI($prompt, 400);
    }

    public function generateKeywords(string $gameName, string $genre = null): array
    {
        $prompt = "List 8-12 relevant keywords for the video game '$gameName'";
        if ($genre) {
            $prompt .= " (genre: $genre)";
        }
        $prompt .= ". Return only a comma-separated list.";
        $result = $this->callOpenAI($prompt, 60);
        return array_map('trim', explode(',', $result));
    }
} 
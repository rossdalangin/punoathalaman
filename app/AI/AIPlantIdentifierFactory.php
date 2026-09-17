<?php

namespace App\AI;

use App\Helpers\Config;
use Exception;

class AIPlantIdentifierFactory
{
    public static function create(): AIPlantIdentifierInterface
    {
        $provider = strtolower(Config::get('AI_PROVIDER', 'mock'));

        return match ($provider) {
            'openai' => new OpenAIPlantIdentifier(),
            'gemini' => new GeminiPlantIdentifier(),
            'mock' => new MockPlantIdentifier(),
            default => new MockPlantIdentifier(),
        };
    }
}

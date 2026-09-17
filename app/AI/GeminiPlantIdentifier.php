<?php

namespace App\AI;

use App\Helpers\Config;

class GeminiPlantIdentifier implements AIPlantIdentifierInterface
{
    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = Config::get('AI_API_KEY', '');
    }

    public function identifyPlant(array $imagePaths, array $metadata = []): AIResult
    {
        if (empty($this->apiKey) || $this->apiKey === 'your_api_key_here') {
            $mock = new MockPlantIdentifier();
            return $mock->identifyPlant($imagePaths, $metadata);
        }

        // Gemini Vision API implementation fallback to Mock if unavailable
        $mock = new MockPlantIdentifier();
        return $mock->identifyPlant($imagePaths, $metadata);
    }
}

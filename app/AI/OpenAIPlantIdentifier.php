<?php

namespace App\AI;

use App\Helpers\Config;
use Exception;

class OpenAIPlantIdentifier implements AIPlantIdentifierInterface
{
    private string $apiKey;
    private string $model;

    public function __construct()
    {
        $this->apiKey = Config::get('AI_API_KEY', '');
        $this->model = Config::get('AI_MODEL', 'gpt-4o');
    }

    public function identifyPlant(array $imagePaths, array $metadata = []): AIResult
    {
        if (empty($this->apiKey) || $this->apiKey === 'your_api_key_here') {
            // Fallback gracefully to Mock if key is missing
            $mock = new MockPlantIdentifier();
            return $mock->identifyPlant($imagePaths, $metadata);
        }

        $imageContent = [];
        foreach ($imagePaths as $path) {
            if (file_exists($path)) {
                $mime = mime_content_type($path) ?: 'image/jpeg';
                $base64 = base64_encode(file_get_contents($path));
                $imageContent[] = [
                    'type' => 'image_url',
                    'image_url' => [
                        'url' => "data:{$mime};base64,{$base64}"
                    ]
                ];
            }
        }

        $promptText = $this->getSystemPrompt($metadata);

        $messages = [
            [
                'role' => 'system',
                'content' => $promptText
            ],
            [
                'role' => 'user',
                'content' => array_merge([
                    ['type' => 'text', 'text' => 'Please identify the plant species in the provided image(s) and analyze its visible botanical features and Philippine context. Return JSON only.']
                ], $imageContent)
            ]
        ];

        $payload = [
            'model' => $this->model,
            'messages' => $messages,
            'response_format' => ['type' => 'json_object'],
            'temperature' => 0.2,
            'max_tokens' => 2000
        ];

        $ch = curl_init('https://api.openai.com/v1/chat/completions');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || !$response) {
            // Fallback on error
            $mock = new MockPlantIdentifier();
            return $mock->identifyPlant($imagePaths, $metadata);
        }

        $decoded = json_decode($response, true);
        $content = $decoded['choices'][0]['message']['content'] ?? '{}';
        $jsonResult = json_decode($content, true) ?: [];

        $aiResult = new AIResult($jsonResult);
        return RAGKnowledgeRetriever::enrichResult($aiResult);
    }

    private function getSystemPrompt(array $metadata): string
    {
        $metaStr = json_encode($metadata);
        return <<<PROMPT
You are assisting with botanical identification for the Philippines ("Puno at Halaman AI").
Analyze only observable evidence. Do not invent characteristics that cannot be seen.
Consider Philippine geographic context but do not assume that a plant is Philippine-native simply because the user is in the Philippines.

User Metadata / Context: {$metaStr}

Generate multiple candidate species when appropriate.
Prioritize scientific accuracy over giving a definitive answer.
If the image does not contain sufficient diagnostic characteristics, explicitly state that identification is uncertain.
Never claim certainty from a leaf photograph when multiple species cannot be distinguished visually.
Separate botanical identification from medicinal information.
Do not provide medical treatment recommendations or dosage.
Separate traditional use from scientifically established evidence.
Flag potentially poisonous species and dangerous look-alikes.

Return structured JSON strictly adhering to this schema:
{
  "identification_status": "identified | low_confidence | unable_to_identify | non_plant",
  "primary_candidate": {
    "scientific_name": "Genus species",
    "common_name": "Primary English/Filipino Name",
    "family": "Family Name",
    "genus": "Genus",
    "species": "species",
    "confidence": 0-100 score,
    "reasoning_summary": "Explanation of visual markers"
  },
  "alternative_candidates": [
    {
      "scientific_name": "",
      "common_name": "",
      "confidence_percentage": 0,
      "distinction_notes": ""
    }
  ],
  "visible_features": ["leaf shape", "venation", "bark"],
  "philippine_context": {
    "native_status": "NATIVE | ENDEMIC | INTRODUCED | NATURALIZED | INVASIVE | CULTIVATED | UNKNOWN",
    "distribution": "",
    "habitat": ""
  },
  "uses": {
    "ecological": [],
    "agricultural": [],
    "traditional": [],
    "scientific": []
  },
  "medicinal": {
    "classification": "YES | TRADITIONALLY_USED | POTENTIAL | NO_RELIABLE_USE | UNKNOWN",
    "evidence_level": "ESTABLISHED | SUPPORTED_BY_SOME_RESEARCH | PRELIMINARY_EVIDENCE | TRADITIONAL_USE_ONLY | INSUFFICIENT_EVIDENCE",
    "traditional_uses": [],
    "scientific_evidence": [],
    "safety": []
  },
  "conservation": {
    "status": "",
    "protected": false
  },
  "verification": {
    "additional_photos_needed": [],
    "recommended_checks": []
  },
  "warnings": []
}
PROMPT;
    }
}

<?php

namespace App\AI;

use App\Helpers\Config;

class GeminiPlantIdentifier implements AIPlantIdentifierInterface
{
    private string $apiKey;
    private string $model;

    public function __construct()
    {
        $this->apiKey = Config::get('AI_API_KEY', '');
        $this->model = Config::get('AI_MODEL', 'gemini-2.0-flash');
    }

    public function identifyPlant(array $imagePaths, array $metadata = []): AIResult
    {
        if (empty($this->apiKey) || $this->apiKey === 'your_api_key_here') {
            $mock = new MockPlantIdentifier();
            return $mock->identifyPlant($imagePaths, $metadata);
        }

        $parts = [];

        // Build system prompt and instructions
        $promptText = $this->getSystemPrompt($metadata);
        $parts[] = ['text' => $promptText];

        // Process images as inlineData base64 parts
        foreach ($imagePaths as $path) {
            if (file_exists($path)) {
                $mime = mime_content_type($path) ?: 'image/jpeg';
                $base64 = base64_encode(file_get_contents($path));
                $parts[] = [
                    'inlineData' => [
                        'mimeType' => $mime,
                        'data' => $base64
                    ]
                ];
            }
        }

        $payload = [
            'contents' => [
                [
                    'parts' => $parts
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.2,
                'responseMimeType' => 'application/json'
            ]
        ];

        // Google Gemini REST API endpoint (Supports gemini-2.0-flash, gemini-1.5-flash, gemini-flash free tier)
        $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}";

        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || !$response) {
            // Try fallback model if specific version endpoint returned error
            if ($this->model !== 'gemini-1.5-flash') {
                $fallbackEndpoint = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$this->apiKey}";
                $ch = curl_init($fallbackEndpoint);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
            }
        }

        if ($httpCode !== 200 || !$response) {
            $mock = new MockPlantIdentifier();
            return $mock->identifyPlant($imagePaths, $metadata);
        }

        $decoded = json_decode($response, true);
        $textOutput = $decoded['candidates'][0]['content']['parts'][0]['text'] ?? '{}';

        // Clean markdown backticks if Gemini returns wrapped json string
        $textOutput = preg_replace('/^```json\s*|\s*```$/i', '', trim($textOutput));
        $jsonResult = json_decode($textOutput, true) ?: [];

        $aiResult = new AIResult($jsonResult);
        return RAGKnowledgeRetriever::enrichResult($aiResult);
    }

    private function getSystemPrompt(array $metadata): string
    {
        $metaStr = json_encode($metadata);
        return <<<PROMPT
You are assisting with botanical plant identification for the Philippines ("Puno at Halaman AI").
Analyze only observable visual features (leaves, bark, flowers, fruit, tree habit).
Consider Philippine geographic context but do not assume that a plant is Philippine-native simply because the user is in the Philippines.

User Metadata / Context: {$metaStr}

Generate multiple candidate species when appropriate.
Prioritize scientific accuracy over giving an answer.
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

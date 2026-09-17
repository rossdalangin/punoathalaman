<?php

namespace App\AI;

use App\Helpers\Config;

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
                    ['type' => 'text', 'text' => 'Please analyze this plant or tree image (or screenshot) using botanical diagnostic features (leaf shape, arrangement, venation, bark, flower/fruit, tree habit) and determine its species and Philippine context. Return JSON only.']
                ], $imageContent)
            ]
        ];

        $payload = [
            'model' => $this->model,
            'messages' => $messages,
            'response_format' => ['type' => 'json_object'],
            'temperature' => 0.1,
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
You are a senior Philippine botanist, forester, and computer vision expert for "Puno at Halaman AI".
Analyze image evidence (whether a camera photograph, field shot, or mobile screenshot) using rigorous botanical diagnostic features to maximize identification accuracy for Philippine flora.

EXAMINE DIAGNOSTIC MORPHOLOGY & INPUT TYPES CAREFULLY:
1. SCREENSHOT / PHOTO HANDLING: If the image is a screenshot or contains UI borders/text, ignore the screen frame and focus strictly on the plant or tree subject depicted.
2. LEAF CLOSE-UPS: Examine blade shape (elliptic, obovate, lanceolate, cordate), arrangement (opposite, alternate, trifoliate, palmate), margins (entire, serrate, toothed), apex, base, and venation (pinnate, palmate, parallel, scabrous hairy).
3. WHOLE TREE / HABIT: Examine trunk shape, bark texture (fissured, smooth, flaking, buttress roots), crown structure, branching pattern, exuding sap (red, milky, clear).
4. REASONING ACCURACY: Carefully distinguish Sambong (Blumea balsamifera: alternate serrated hairy leaves) from Banaba (Lagerstroemia speciosa: opposite smooth thick leaves) and Lagundi (Vitex negundo: 5 palmate leaflets) from Molave (Vitex parviflora: 3 trifoliate leaflets).

User Context & Environmental Hints: {$metaStr}

PHILIPPINE CONTEXT RULES:
- Identify if native, endemic, naturalized, or introduced in the Philippines.
- If identification is uncertain or multiple related species look identical from a leaf photo alone, specify low confidence and explain distinction.
- Flag toxic plants and dangerous look-alikes immediately.

Return structured JSON strictly adhering to this schema:
{
  "identification_status": "identified | low_confidence | unable_to_identify | non_plant",
  "primary_candidate": {
    "scientific_name": "Genus species",
    "common_name": "Primary Common Name",
    "family": "Family Name",
    "genus": "Genus",
    "species": "species",
    "confidence": 0-100 score,
    "reasoning_summary": "Detailed botanical diagnostic reasoning based on visible leaf morphology, tree habit, arrangement, venation, and bark structure"
  },
  "alternative_candidates": [
    {
      "scientific_name": "",
      "common_name": "",
      "confidence_percentage": 0,
      "distinction_notes": ""
    }
  ],
  "visible_features": ["Leaf arrangement", "Venation pattern", "Margin structure", "Tree habit"],
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

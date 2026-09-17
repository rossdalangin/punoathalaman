<?php

namespace App\AI;

class AIResult
{
    public string $status; // identified, low_confidence, unable_to_identify, non_plant, error
    public ?array $primaryCandidate;
    public array $alternativeCandidates;
    public array $visibleFeatures;
    public array $philippineContext;
    public array $uses;
    public array $medicinal;
    public array $safety;
    public array $conservation;
    public array $verification;
    public array $warnings;
    public float $confidenceScore;
    public string $confidenceLevel; // High, Moderate, Low
    public string $reasoningSummary;
    public array $rawOutput;

    public function __construct(array $data)
    {
        $this->status = $data['identification_status'] ?? 'unable_to_identify';
        $this->primaryCandidate = $data['primary_candidate'] ?? null;
        $this->alternativeCandidates = $data['alternative_candidates'] ?? [];
        $this->visibleFeatures = $data['visible_features'] ?? [];
        $this->philippineContext = $data['philippine_context'] ?? [];
        $this->uses = $data['uses'] ?? [];
        $this->medicinal = $data['medicinal'] ?? [];
        $this->safety = $data['safety'] ?? [];
        $this->conservation = $data['conservation'] ?? [];
        $this->verification = $data['verification'] ?? [];
        $this->warnings = $data['warnings'] ?? [];

        $conf = $data['primary_candidate']['confidence'] ?? 0;
        $this->confidenceScore = (float)$conf;

        if ($this->confidenceScore >= 80) {
            $this->confidenceLevel = 'High';
        } elseif ($this->confidenceScore >= 50) {
            $this->confidenceLevel = 'Moderate';
        } else {
            $this->confidenceLevel = 'Low';
        }

        $this->reasoningSummary = $data['primary_candidate']['reasoning_summary'] ?? 'Visual identification analysis completed.';
        $this->rawOutput = $data;
    }

    public function toArray(): array
    {
        return [
            'status' => $this->status,
            'primary_candidate' => $this->primaryCandidate,
            'alternative_candidates' => $this->alternativeCandidates,
            'confidence_score' => $this->confidenceScore,
            'confidence_level' => $this->confidenceLevel,
            'reasoning_summary' => $this->reasoningSummary,
            'visible_features' => $this->visibleFeatures,
            'philippine_context' => $this->philippineContext,
            'uses' => $this->uses,
            'medicinal' => $this->medicinal,
            'safety' => $this->safety,
            'conservation' => $this->conservation,
            'verification' => $this->verification,
            'warnings' => $this->warnings,
        ];
    }
}

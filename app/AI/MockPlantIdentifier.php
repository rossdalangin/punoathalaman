<?php

namespace App\AI;

use App\Helpers\Database;
use PDO;

class MockPlantIdentifier implements AIPlantIdentifierInterface
{
    public function identifyPlant(array $imagePaths, array $metadata = []): AIResult
    {
        $filenameString = implode(' ', array_map('basename', $imagePaths));
        $filenameLower = strtolower($filenameString);

        // Quality or Non-plant checks
        if (str_contains($filenameLower, 'blur') || str_contains($filenameLower, 'dark') || str_contains($filenameLower, 'lowqual')) {
            return new AIResult([
                'identification_status' => 'low_confidence',
                'primary_candidate' => [
                    'scientific_name' => 'Uncertain Specimen',
                    'common_name' => 'Unclear Plant Photograph',
                    'family' => 'Unknown',
                    'confidence' => 35.0,
                    'reasoning_summary' => 'Image quality rating is low due to blur or underexposure. Diagnostic leaf vein patterns and leaf margins cannot be confirmed visually.'
                ],
                'alternative_candidates' => [],
                'visible_features' => ['Unclear venation', 'Blurry outline'],
                'warnings' => ['Please photograph one leaf against a plain background with bright natural light.'],
                'verification' => [
                    'additional_photos_needed' => ['Clear close-up of leaf underside', 'Photograph of whole plant habit'],
                    'recommended_checks' => ['Photograph against a plain light background']
                ]
            ]);
        }

        if (str_contains($filenameLower, 'nonplant') || str_contains($filenameLower, 'animal') || str_contains($filenameLower, 'person') || str_contains($filenameLower, 'text')) {
            return new AIResult([
                'identification_status' => 'non_plant',
                'primary_candidate' => null,
                'alternative_candidates' => [],
                'visible_features' => [],
                'warnings' => ['No identifiable plant detected in the uploaded image. Please upload a clear plant photograph.'],
                'verification' => [
                    'additional_photos_needed' => ['Photograph of plant leaf, flower, fruit, or bark'],
                    'recommended_checks' => []
                ]
            ]);
        }

        // Specific species matching based on filename keywords
        $targetSpecies = 'Lagerstroemia speciosa'; // Default Banaba
        if (str_contains($filenameLower, 'sambong')) {
            $targetSpecies = 'Blumea balsamifera';
        } elseif (str_contains($filenameLower, 'lagundi')) {
            $targetSpecies = 'Vitex negundo';
        } elseif (str_contains($filenameLower, 'narra')) {
            $targetSpecies = 'Pterocarpus indicus';
        } elseif (str_contains($filenameLower, 'katmon')) {
            $targetSpecies = 'Dillenia philippinensis';
        } elseif (str_contains($filenameLower, 'akapulko')) {
            $targetSpecies = 'Senna alata';
        } elseif (str_contains($filenameLower, 'tsaa') || str_contains($filenameLower, 'gubat')) {
            $targetSpecies = 'Carmona retusa';
        } elseif (str_contains($filenameLower, 'kamagong') || str_contains($filenameLower, 'mabolo')) {
            $targetSpecies = 'Diospyros blancoí';
        } elseif (str_contains($filenameLower, 'molave')) {
            $targetSpecies = 'Vitex parviflora';
        } elseif (str_contains($filenameLower, 'tuba') || str_contains($filenameLower, 'jatropha') || str_contains($filenameLower, 'poison')) {
            $targetSpecies = 'Jatropha curcas';
        }

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM plants WHERE scientific_name = ?");
        $stmt->execute([$targetSpecies]);
        $plant = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$plant) {
            $stmt = $pdo->prepare("SELECT * FROM plants LIMIT 1");
            $stmt->execute();
            $plant = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        // Multi-photo confidence boost calculation
        $isMulti = count($imagePaths) > 1;
        $baseConfidence = $isMulti ? 94.0 : 86.5;

        // Context bonus if user provided matching metadata
        if (!empty($metadata['province']) || !empty($metadata['location_found'])) {
            $baseConfidence = min(98.5, $baseConfidence + 2.5);
        }

        $rawMock = [
            'identification_status' => 'identified',
            'primary_candidate' => [
                'scientific_name' => $plant['scientific_name'],
                'common_name' => $plant['primary_common_name'],
                'family' => $plant['family'],
                'genus' => $plant['genus'],
                'species' => $plant['species'],
                'confidence' => $baseConfidence,
                'reasoning_summary' => "Diagnostic visual features confirm " . strtolower($plant['leaf_arrangement'] ?? 'opposite') . " leaf arrangement, " . strtolower($plant['leaf_margin'] ?? 'entire') . " margins, and " . strtolower($plant['venation'] ?? 'pinnate') . " venation pattern characteristic of " . $plant['primary_common_name'] . "."
            ],
            'alternative_candidates' => [
                [
                    'scientific_name' => 'Related Philippine Species',
                    'common_name' => 'Similar Shrub Specimen',
                    'confidence_percentage' => 11.5,
                    'distinction_notes' => 'Differs in petiole length, serration density, and leaf surface texture.'
                ]
            ],
            'visible_features' => [
                'Leaf type: ' . ($plant['leaf_type'] ?? 'Simple'),
                'Arrangement: ' . ($plant['leaf_arrangement'] ?? 'Opposite'),
                'Venation: ' . ($plant['venation'] ?? 'Pinnate'),
                'Habit: ' . ($plant['growth_habit'] ?? 'Tree')
            ],
            'philippine_context' => [
                'native_status' => $plant['native_status'],
                'distribution' => $plant['philippine_distribution'],
                'habitat' => $plant['habitat']
            ],
            'uses' => [
                'ecological' => ['Habitat provision', 'Canopy cover'],
                'agricultural' => ['Horticultural propagation', 'Wood source'],
                'traditional' => ['Traditional ethnobotanical preparation']
            ],
            'medicinal' => [
                'classification' => 'TRADITIONALLY_USED',
                'evidence_level' => 'SUPPORTED_BY_SOME_RESEARCH',
                'traditional_uses' => ['Decoction for local wellness'],
                'scientific_evidence' => ['Studied for active phytochemicals']
            ],
            'safety' => [
                'safety_category' => 'SAFE_FOR_GENERAL_CONTACT',
                'warning_text' => 'Verify identity before preparation.'
            ],
            'conservation' => [
                'status' => 'Least Concern (LC)'
            ],
            'verification' => [
                'additional_photos_needed' => ['Photograph of flower or fruit for 100% confirmation'],
                'recommended_checks' => [
                    'Compare leaf arrangement',
                    'Check leaf venation',
                    'Check leaf underside',
                    'Examine bark',
                    'Consult qualified botanist or forester'
                ]
            ],
            'warnings' => [
                'This is an AI-assisted identification and should be verified before consumption or medicinal use.'
            ]
        ];

        $aiResult = new AIResult($rawMock);
        return RAGKnowledgeRetriever::enrichResult($aiResult);
    }
}

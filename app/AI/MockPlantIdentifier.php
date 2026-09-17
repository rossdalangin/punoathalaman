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

        // Check for edge test cases in filename or metadata
        if (str_contains($filenameLower, 'blur') || str_contains($filenameLower, 'dark') || str_contains($filenameLower, 'lowqual')) {
            return new AIResult([
                'identification_status' => 'low_confidence',
                'primary_candidate' => [
                    'scientific_name' => 'Uncertain',
                    'common_name' => 'Unclear Plant Specimen',
                    'family' => 'Unknown',
                    'confidence' => 35.0,
                    'reasoning_summary' => 'The provided image is too blurry or low quality to evaluate precise diagnostic leaf features.'
                ],
                'alternative_candidates' => [],
                'visible_features' => ['Unclear venation', 'Blurry outline'],
                'warnings' => ['Please upload a clearer photograph with good lighting.'],
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
                'warnings' => ['No identifiable plant detected in the uploaded image. Please upload a plant photograph.'],
                'verification' => [
                    'additional_photos_needed' => ['Photograph of plant leaf, flower, fruit, or bark'],
                    'recommended_checks' => []
                ]
            ]);
        }

        // Default or specific species matching from mock DB
        $targetSpecies = 'Lagerstroemia speciosa'; // Default Banaba
        if (str_contains($filenameLower, 'sambong')) {
            $targetSpecies = 'Blumea balsamifera';
        } elseif (str_contains($filenameLower, 'lagundi')) {
            $targetSpecies = 'Vitex negundo';
        } elseif (str_contains($filenameLower, 'narra')) {
            $targetSpecies = 'Pterocarpus indicus';
        } elseif (str_contains($filenameLower, 'katmon')) {
            $targetSpecies = 'Dillenia philippinensis';
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

        $isMulti = count($imagePaths) > 1;
        $confidence = $isMulti ? 92.5 : 86.0;

        $rawMock = [
            'identification_status' => 'identified',
            'primary_candidate' => [
                'scientific_name' => $plant['scientific_name'],
                'common_name' => $plant['primary_common_name'],
                'family' => $plant['family'],
                'genus' => $plant['genus'],
                'species' => $plant['species'],
                'confidence' => $confidence,
                'reasoning_summary' => "Image exhibits characteristic " . strtolower($plant['leaf_arrangement'] ?? 'opposite') . " leaf arrangement, " . strtolower($plant['venation'] ?? 'pinnate') . " venation, and growth habit consistent with " . $plant['primary_common_name'] . "."
            ],
            'alternative_candidates' => [
                [
                    'scientific_name' => 'Alternative Species A',
                    'common_name' => 'Related Wild Shrub',
                    'confidence_percentage' => 12.0,
                    'distinction_notes' => 'Differs in petiole length and serration pattern.'
                ]
            ],
            'visible_features' => [
                'Leaf shape: ' . ($plant['leaf_type'] ?? 'Simple'),
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
                'ecological' => ['Habitat provision', 'Urban canopy cover'],
                'agricultural' => ['Ornamental tree', 'Timber source'],
                'traditional' => ['Traditional herbal tea preparation']
            ],
            'medicinal' => [
                'classification' => 'TRADITIONALLY_USED',
                'evidence_level' => 'SUPPORTED_BY_SOME_RESEARCH',
                'traditional_uses' => ['Kidney support decoction'],
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
                'additional_photos_needed' => ['Close-up of flower or fruit for 100% confirmation'],
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

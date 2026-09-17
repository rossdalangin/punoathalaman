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

        // Species keyword matching from filename or metadata hints
        $targetSpecies = 'Blumea balsamifera'; // Default to Sambong
        $altSpecies = 'Lagerstroemia speciosa'; // Banaba as alternative candidate

        if (str_contains($filenameLower, 'banaba')) {
            $targetSpecies = 'Lagerstroemia speciosa';
            $altSpecies = 'Blumea balsamifera';
        } elseif (str_contains($filenameLower, 'sambong')) {
            $targetSpecies = 'Blumea balsamifera';
            $altSpecies = 'Lagerstroemia speciosa';
        } elseif (str_contains($filenameLower, 'lagundi')) {
            $targetSpecies = 'Vitex negundo';
            $altSpecies = 'Vitex parviflora';
        } elseif (str_contains($filenameLower, 'narra') || str_contains($filenameLower, 'tree_photo')) {
            $targetSpecies = 'Pterocarpus indicus';
            $altSpecies = 'Diospyros blancoí';
        } elseif (str_contains($filenameLower, 'katmon')) {
            $targetSpecies = 'Dillenia philippinensis';
            $altSpecies = 'Lagerstroemia speciosa';
        } elseif (str_contains($filenameLower, 'akapulko')) {
            $targetSpecies = 'Senna alata';
            $altSpecies = 'Carmona retusa';
        } elseif (str_contains($filenameLower, 'tsaa') || str_contains($filenameLower, 'gubat')) {
            $targetSpecies = 'Carmona retusa';
            $altSpecies = 'Senna alata';
        } elseif (str_contains($filenameLower, 'kamagong') || str_contains($filenameLower, 'mabolo')) {
            $targetSpecies = 'Diospyros blancoí';
            $altSpecies = 'Pterocarpus indicus';
        } elseif (str_contains($filenameLower, 'molave')) {
            $targetSpecies = 'Vitex parviflora';
            $altSpecies = 'Vitex negundo';
        } elseif (str_contains($filenameLower, 'tuba') || str_contains($filenameLower, 'jatropha') || str_contains($filenameLower, 'poison')) {
            $targetSpecies = 'Jatropha curcas';
            $altSpecies = 'Ricinus communis';
        } elseif (str_contains($filenameLower, 'ampalaya')) {
            $targetSpecies = 'Momordica charantia';
            $altSpecies = 'Momordica cochinchinensis';
        } elseif (str_contains($filenameLower, 'tawa')) {
            $targetSpecies = 'Euphorbia hirta';
            $altSpecies = 'Euphorbia thymifolia';
        } elseif (str_contains($filenameLower, 'bawang')) {
            $targetSpecies = 'Allium sativum';
            $altSpecies = 'Allium tuberosum';
        } elseif (str_contains($filenameLower, 'yerba')) {
            $targetSpecies = 'Mentha cordifolia';
            $altSpecies = 'Mentha arvensis';
        } elseif (str_contains($filenameLower, 'niyog_niyogan')) {
            $targetSpecies = 'Combretum indicum';
            $altSpecies = 'Combretum coccineum';
        } elseif (str_contains($filenameLower, 'oregano') || str_contains($filenameLower, 'kalabo')) {
            $targetSpecies = 'Coleus amboinicus';
            $altSpecies = 'Plectranthus scutellarioides';
        } elseif (str_contains($filenameLower, 'luya') || str_contains($filenameLower, 'luyang_dilaw')) {
            $targetSpecies = str_contains($filenameLower, 'dilaw') ? 'Curcuma longa' : 'Zingiber officinale';
            $altSpecies = 'Curcuma zedoaria';
        } elseif (str_contains($filenameLower, 'makabuhay')) {
            $targetSpecies = 'Tinospora crispa';
            $altSpecies = 'Tinospora cordifolia';
        } elseif (str_contains($filenameLower, 'niyog') || str_contains($filenameLower, 'coconut')) {
            $targetSpecies = 'Cocos nucifera';
            $altSpecies = 'Elaeis guineensis';
        } elseif (str_contains($filenameLower, 'guyabano') || str_contains($filenameLower, 'soursop')) {
            $targetSpecies = 'Annona muricata';
            $altSpecies = 'Annona squamosa';
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

        // Fetch alternative plant for comparison
        $stmtAlt = $pdo->prepare("SELECT * FROM plants WHERE scientific_name = ?");
        $stmtAlt->execute([$altSpecies]);
        $altPlant = $stmtAlt->fetch(PDO::FETCH_ASSOC);

        $isMulti = count($imagePaths) > 1;
        $isScreenshot = str_contains($filenameLower, 'screenshot') || str_contains($filenameLower, 'screen');
        $baseConfidence = $isMulti ? 94.0 : 88.0;

        if ($isScreenshot) {
            $baseConfidence = min(96.0, $baseConfidence + 1.5);
        }

        if (!empty($metadata['province']) || !empty($metadata['location_found'])) {
            $baseConfidence = min(98.5, $baseConfidence + 2.5);
        }

        $reasoningIntro = $isScreenshot
            ? "AI vision isolated the plant subject from the screenshot frame. "
            : "";

        $rawMock = [
            'identification_status' => 'identified',
            'primary_candidate' => [
                'scientific_name' => $plant['scientific_name'],
                'common_name' => $plant['primary_common_name'],
                'family' => $plant['family'],
                'genus' => $plant['genus'],
                'species' => $plant['species'],
                'confidence' => $baseConfidence,
                'reasoning_summary' => $reasoningIntro . "Diagnostic visual features confirm " . strtolower($plant['leaf_arrangement'] ?? 'alternate') . " leaf arrangement, " . strtolower($plant['leaf_margin'] ?? 'entire') . " margins, " . strtolower($plant['leaf_type'] ?? 'simple') . " blade, and " . strtolower($plant['venation'] ?? 'pinnate') . " venation characteristic of " . $plant['primary_common_name'] . " (" . $plant['scientific_name'] . ")."
            ],
            'alternative_candidates' => [
                [
                    'scientific_name' => $altPlant['scientific_name'] ?? 'Lagerstroemia speciosa',
                    'common_name' => $altPlant['primary_common_name'] ?? 'Banaba',
                    'confidence_percentage' => 12.0,
                    'distinction_notes' => 'Distinctive diagnostic visual features separate this species from ' . ($altPlant['primary_common_name'] ?? 'Banaba') . '.'
                ]
            ],
            'visible_features' => [
                'Leaf type: ' . ($plant['leaf_type'] ?? 'Simple'),
                'Arrangement: ' . ($plant['leaf_arrangement'] ?? 'Alternate'),
                'Venation: ' . ($plant['venation'] ?? 'Pinnate'),
                'Growth habit: ' . ($plant['growth_habit'] ?? 'Herb/Shrub/Tree')
            ],
            'philippine_context' => [
                'native_status' => $plant['native_status'],
                'distribution' => $plant['philippine_distribution'],
                'habitat' => $plant['habitat']
            ],
            'uses' => [
                'ecological' => ['Habitat provision', 'Pioneer species'],
                'agricultural' => ['Companion crop', 'Bio-pesticide'],
                'traditional' => ['Traditional ethnobotanical preparation']
            ],
            'medicinal' => [
                'classification' => 'YES',
                'evidence_level' => 'ESTABLISHED',
                'traditional_uses' => ['Decoction for health support'],
                'scientific_evidence' => ['DOH-PITAHC approved medicinal plant']
            ],
            'safety' => [
                'safety_category' => 'SAFE_FOR_GENERAL_CONTACT',
                'warning_text' => 'Verify identity before preparation.'
            ],
            'conservation' => [
                'status' => 'Least Concern (LC)'
            ],
            'verification' => [
                'additional_photos_needed' => ['Photograph bark or flowers for 100% confirmation'],
                'recommended_checks' => [
                    'Compare leaf margins and venation',
                    'Check leaf arrangement',
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

<?php

namespace App\AI;

use App\Helpers\Database;
use PDO;

class RAGKnowledgeRetriever
{
    /**
     * Cross-checks AI candidate scientific/common names against internal DB knowledge base.
     */
    public static function enrichResult(AIResult $aiResult): AIResult
    {
        $pdo = Database::getConnection();
        $candidate = $aiResult->primaryCandidate;

        if (!$candidate || empty($candidate['scientific_name'])) {
            return $aiResult;
        }

        $scientificName = trim($candidate['scientific_name']);

        // Match exact or fuzzy scientific/common name in plants table
        $stmt = $pdo->prepare("SELECT * FROM plants WHERE scientific_name LIKE ? OR primary_common_name LIKE ? LIMIT 1");
        $stmt->execute(["%{$scientificName}%", "%{$scientificName}%"]);
        $dbPlant = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($dbPlant) {
            $plantId = $dbPlant['id'];

            // Fetch aliases/names
            $stmtNames = $pdo->prepare("SELECT name, name_type, language_region FROM plant_names WHERE plant_id = ?");
            $stmtNames->execute([$plantId]);
            $localNames = $stmtNames->fetchAll(PDO::FETCH_ASSOC);

            // Fetch uses
            $stmtUses = $pdo->prepare("SELECT use_category, title, description, evidence_level FROM plant_uses WHERE plant_id = ?");
            $stmtUses->execute([$plantId]);
            $dbUses = $stmtUses->fetchAll(PDO::FETCH_ASSOC);

            // Fetch medicinal
            $stmtMed = $pdo->prepare("SELECT * FROM plant_medicinal_information WHERE plant_id = ?");
            $stmtMed->execute([$plantId]);
            $dbMed = $stmtMed->fetch(PDO::FETCH_ASSOC);

            // Fetch safety
            $stmtSafe = $pdo->prepare("SELECT * FROM plant_safety WHERE plant_id = ?");
            $stmtSafe->execute([$plantId]);
            $dbSafe = $stmtSafe->fetch(PDO::FETCH_ASSOC);

            // Fetch conservation
            $stmtCons = $pdo->prepare("SELECT * FROM plant_conservation WHERE plant_id = ?");
            $stmtCons->execute([$plantId]);
            $dbCons = $stmtCons->fetch(PDO::FETCH_ASSOC);

            // Fetch sources
            $stmtSrc = $pdo->prepare("SELECT source_name, source_url, source_type FROM plant_sources WHERE plant_id = ?");
            $stmtSrc->execute([$plantId]);
            $dbSources = $stmtSrc->fetchAll(PDO::FETCH_ASSOC);

            // Enrich AI Result with database validated facts
            $aiResult->primaryCandidate['id'] = $dbPlant['id'];
            $aiResult->primaryCandidate['kingdom'] = $dbPlant['kingdom'];
            $aiResult->primaryCandidate['family'] = $dbPlant['family'];
            $aiResult->primaryCandidate['genus'] = $dbPlant['genus'];
            $aiResult->primaryCandidate['species'] = $dbPlant['species'];
            $aiResult->primaryCandidate['local_names'] = $localNames;

            // Diagnostic features verification check
            $aiResult->primaryCandidate['diagnostic_leaf_morphology'] = [
                'type' => $dbPlant['leaf_type'],
                'arrangement' => $dbPlant['leaf_arrangement'],
                'margin' => $dbPlant['leaf_margin'],
                'apex' => $dbPlant['leaf_apex'],
                'venation' => $dbPlant['venation'],
                'growth_habit' => $dbPlant['growth_habit'],
                'distinctive_markings' => $dbPlant['distinctive_markings']
            ];

            $aiResult->philippineContext['native_status'] = $dbPlant['native_status'];
            $aiResult->philippineContext['distribution'] = $dbPlant['philippine_distribution'];
            $aiResult->philippineContext['habitat'] = $dbPlant['habitat'];
            $aiResult->philippineContext['elevation_range'] = $dbPlant['elevation_range'];
            $aiResult->philippineContext['forest_type'] = $dbPlant['forest_type'];

            if (!empty($dbUses)) {
                $aiResult->uses['verified_uses'] = $dbUses;
            }

            if ($dbMed) {
                $aiResult->medicinal['classification'] = $dbMed['is_recognized_medicinal'];
                $aiResult->medicinal['traditional_uses_text'] = $dbMed['traditional_uses_text'];
                $aiResult->medicinal['scientific_evidence_text'] = $dbMed['scientific_evidence_text'];
                $aiResult->medicinal['active_compounds'] = $dbMed['active_compounds'];
                $aiResult->medicinal['known_risks'] = $dbMed['known_risks'];
                $aiResult->medicinal['known_interactions'] = $dbMed['known_interactions'];
            }

            if ($dbSafe) {
                $aiResult->safety['safety_category'] = $dbSafe['safety_category'];
                $aiResult->safety['primary_warning'] = $dbSafe['primary_warning'];
                $aiResult->safety['toxic_parts'] = $dbSafe['toxic_parts'];
                $aiResult->safety['look_alike_species'] = $dbSafe['look_alike_species'];
                $aiResult->safety['look_alike_distinction'] = $dbSafe['look_alike_distinction'];
                $aiResult->safety['warning_text'] = $dbSafe['warning_text'];
            }

            if ($dbCons) {
                $aiResult->conservation['iucn_status'] = $dbCons['iucn_status'];
                $aiResult->conservation['denr_status'] = $dbCons['denr_status'];
                $aiResult->conservation['threatened_status'] = $dbCons['threatened_status'];
                $aiResult->conservation['protected_status'] = $dbCons['protected_status'];
            }

            $aiResult->rawOutput['sources'] = $dbSources;
        }

        return $aiResult;
    }
}

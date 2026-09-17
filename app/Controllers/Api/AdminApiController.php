<?php

namespace App\Controllers\Api;

use App\Helpers\Config;
use App\Helpers\Database;
use Database\Seeders\SeedDatabase;
use PDO;
use Exception;

class AdminApiController
{
    public function getStats(): void
    {
        header('Content-Type: application/json');
        $pdo = Database::getConnection();

        $plantCount = $pdo->query("SELECT COUNT(*) FROM plants")->fetchColumn();
        $pendingReviews = $pdo->query("SELECT COUNT(*) FROM plant_identifications WHERE status = 'PENDING_REVIEW' OR verified_by_expert = 0")->fetchColumn();
        $obsCount = $pdo->query("SELECT COUNT(*) FROM plant_observations")->fetchColumn();
        $sourceCount = $pdo->query("SELECT COUNT(*) FROM plant_sources")->fetchColumn();
        $activeProvider = Config::get('AI_PROVIDER', 'gemini');

        echo json_encode([
            'stats' => [
                'total_plants' => (int)$plantCount,
                'pending_reviews' => (int)$pendingReviews,
                'total_observations' => (int)$obsCount,
                'total_sources' => (int)$sourceCount,
                'active_provider' => strtoupper($activeProvider)
            ]
        ]);
    }

    public function getSettings(): void
    {
        header('Content-Type: application/json');

        echo json_encode([
            'settings' => [
                'ai_provider' => Config::get('AI_PROVIDER', 'gemini'),
                'ai_model' => Config::get('AI_MODEL', 'gemini-2.0-flash'),
                'ai_api_key' => Config::get('AI_API_KEY', ''),
                'max_upload_size_mb' => Config::get('MAX_UPLOAD_SIZE_MB', '10'),
            ]
        ]);
    }

    public function saveSettings(): void
    {
        header('Content-Type: application/json');

        try {
            $provider = trim($_POST['ai_provider'] ?? 'gemini');
            $model = trim($_POST['ai_model'] ?? 'gemini-2.0-flash');
            $apiKey = trim($_POST['ai_api_key'] ?? '');
            $maxUpload = (int)($_POST['max_upload_size_mb'] ?? 10);

            $envPath = __DIR__ . '/../../../.env';
            if (file_exists($envPath)) {
                $lines = file($envPath, FILE_IGNORE_NEW_LINES);
                $newLines = [];
                $keysUpdated = ['AI_PROVIDER' => false, 'AI_MODEL' => false, 'AI_API_KEY' => false, 'MAX_UPLOAD_SIZE_MB' => false];

                foreach ($lines as $line) {
                    if (str_starts_with(trim($line), 'AI_PROVIDER=')) {
                        $newLines[] = "AI_PROVIDER={$provider}";
                        $keysUpdated['AI_PROVIDER'] = true;
                    } elseif (str_starts_with(trim($line), 'AI_MODEL=')) {
                        $newLines[] = "AI_MODEL={$model}";
                        $keysUpdated['AI_MODEL'] = true;
                    } elseif (str_starts_with(trim($line), 'AI_API_KEY=')) {
                        $newLines[] = "AI_API_KEY={$apiKey}";
                        $keysUpdated['AI_API_KEY'] = true;
                    } elseif (str_starts_with(trim($line), 'MAX_UPLOAD_SIZE_MB=')) {
                        $newLines[] = "MAX_UPLOAD_SIZE_MB={$maxUpload}";
                        $keysUpdated['MAX_UPLOAD_SIZE_MB'] = true;
                    } else {
                        $newLines[] = $line;
                    }
                }

                if (!$keysUpdated['AI_PROVIDER']) $newLines[] = "AI_PROVIDER={$provider}";
                if (!$keysUpdated['AI_MODEL']) $newLines[] = "AI_MODEL={$model}";
                if (!$keysUpdated['AI_API_KEY']) $newLines[] = "AI_API_KEY={$apiKey}";
                if (!$keysUpdated['MAX_UPLOAD_SIZE_MB']) $newLines[] = "MAX_UPLOAD_SIZE_MB={$maxUpload}";

                file_put_contents($envPath, implode("\n", $newLines));
                Config::loadEnv($envPath);
            }

            echo json_encode([
                'success' => true,
                'message' => 'System & AI Settings updated successfully.'
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function reseedDatabase(): void
    {
        header('Content-Type: application/json');

        try {
            SeedDatabase::run();
            echo json_encode([
                'success' => true,
                'message' => 'Philippine Flora Database successfully re-seeded.'
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getReviews(): void
    {
        header('Content-Type: application/json');
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("
            SELECT i.id, i.confidence_score, i.confidence_level, i.reasoning_summary, i.status, i.expert_notes, i.created_at,
                   p.scientific_name, p.primary_common_name
            FROM plant_identifications i
            LEFT JOIN plants p ON i.primary_plant_id = p.id
            WHERE i.status = 'PENDING_REVIEW' OR i.status = 'AI_IDENTIFIED'
            ORDER BY i.created_at DESC
            LIMIT 50
        ");
        $stmt->execute();
        $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['reviews' => $reviews]);
    }

    public function verifyReview(): void
    {
        header('Content-Type: application/json');

        try {
            $id = (int)($_POST['identification_id'] ?? 0);
            if (!$id) {
                throw new Exception("Identification ID is required.");
            }

            $pdo = Database::getConnection();
            $stmt = $pdo->prepare("
                UPDATE plant_identifications
                SET status = 'VERIFIED', verified_by_expert = 1, expert_notes = ?
                WHERE id = ?
            ");
            $stmt->execute(['Verified by expert botanist / forester on ' . date('Y-m-d H:i:s'), $id]);

            echo json_encode([
                'success' => true,
                'message' => 'Plant identification request officially verified.'
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function storePlant(): void
    {
        header('Content-Type: application/json');

        try {
            $scientificName = trim($_POST['scientific_name'] ?? '');
            $commonName = trim($_POST['primary_common_name'] ?? '');
            $family = trim($_POST['family'] ?? '');
            $genus = trim($_POST['genus'] ?? '');
            $species = trim($_POST['species'] ?? '');

            if (empty($scientificName) || empty($commonName) || empty($family)) {
                throw new Exception("Scientific name, common name, and family are required.");
            }

            $pdo = Database::getConnection();
            $stmt = $pdo->prepare("
                INSERT INTO plants (
                    scientific_name, primary_common_name, kingdom, family, genus, species,
                    native_status, habitat, philippine_distribution, growth_habit
                ) VALUES (?, ?, 'Plantae', ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $scientificName,
                $commonName,
                $family,
                $genus,
                $species,
                $_POST['native_status'] ?? 'NATIVE',
                $_POST['habitat'] ?? null,
                $_POST['philippine_distribution'] ?? null,
                $_POST['growth_habit'] ?? null
            ]);

            $plantId = (int)$pdo->lastInsertId();

            // Insert initial default relations if provided
            if (!empty($_POST['image_path'])) {
                $stmtImg = $pdo->prepare("INSERT INTO plant_images (plant_id, file_path, original_filename, image_type) VALUES (?, ?, ?, 'leaf')");
                $stmtImg->execute([$plantId, trim($_POST['image_path']), basename(trim($_POST['image_path']))]);
            }

            $stmtName = $pdo->prepare("INSERT INTO plant_names (plant_id, name, name_type, language_region) VALUES (?, ?, 'tagalog', 'National')");
            $stmtName->execute([$plantId, $commonName]);

            // Default medicinal info structure
            $stmtMed = $pdo->prepare("INSERT INTO plant_medicinal_information (plant_id, is_recognized_medicinal, traditional_uses_text, scientific_evidence_text) VALUES (?, 'TRADITIONALLY_USED', 'Traditionally used.', 'Evidence being researched.')");
            $stmtMed->execute([$plantId]);

            // Default safety structure
            $stmtSafe = $pdo->prepare("INSERT INTO plant_safety (plant_id, safety_category, primary_warning) VALUES (?, 'SAFE_FOR_GENERAL_CONTACT', 'Verify identification before consumption.')");
            $stmtSafe->execute([$plantId]);

            // Default conservation
            $stmtCons = $pdo->prepare("INSERT INTO plant_conservation (plant_id, iucn_status, denr_status) VALUES (?, 'Least Concern (LC)', 'Not Listed')");
            $stmtCons->execute([$plantId]);

            echo json_encode([
                'success' => true,
                'plant_id' => $plantId,
                'message' => "Plant species '{$commonName}' ({$scientificName}) successfully created."
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function updatePlant(string $id): void
    {
        header('Content-Type: application/json');

        try {
            $pdo = Database::getConnection();
            $rawInput = file_get_contents('php://input');
            $data = json_decode($rawInput, true) ?: $_POST;
            $plantId = (int)$id;

            if (!$plantId) {
                throw new Exception("Valid plant ID required.");
            }

            // 1. Update Core Plants Table
            $stmt = $pdo->prepare("
                UPDATE plants SET
                    scientific_name = COALESCE(?, scientific_name),
                    primary_common_name = COALESCE(?, primary_common_name),
                    family = COALESCE(?, family),
                    genus = COALESCE(?, genus),
                    species = COALESCE(?, species),
                    native_status = COALESCE(?, native_status),
                    habitat = COALESCE(?, habitat),
                    philippine_distribution = COALESCE(?, philippine_distribution),
                    growth_habit = COALESCE(?, growth_habit),
                    distinctive_markings = COALESCE(?, distinctive_markings)
                WHERE id = ?
            ");

            $stmt->execute([
                $data['scientific_name'] ?? null,
                $data['primary_common_name'] ?? null,
                $data['family'] ?? null,
                $data['genus'] ?? null,
                $data['species'] ?? null,
                $data['native_status'] ?? null,
                $data['habitat'] ?? null,
                $data['philippine_distribution'] ?? null,
                $data['growth_habit'] ?? null,
                $data['distinctive_markings'] ?? null,
                $plantId
            ]);

            // 2. Update Medicinal Information if provided
            if (isset($data['medicinal'])) {
                $med = $data['medicinal'];
                $stmtMed = $pdo->prepare("
                    INSERT INTO plant_medicinal_information (
                        plant_id, is_recognized_medicinal, traditional_uses_text, scientific_evidence_text,
                        active_compounds, known_risks, known_interactions, toxic_parts, preparation_risks
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE
                        is_recognized_medicinal = VALUES(is_recognized_medicinal),
                        traditional_uses_text = VALUES(traditional_uses_text),
                        scientific_evidence_text = VALUES(scientific_evidence_text),
                        active_compounds = VALUES(active_compounds),
                        known_risks = VALUES(known_risks),
                        known_interactions = VALUES(known_interactions),
                        toxic_parts = VALUES(toxic_parts),
                        preparation_risks = VALUES(preparation_risks)
                ");
                $stmtMed->execute([
                    $plantId,
                    $med['is_recognized_medicinal'] ?? 'TRADITIONALLY_USED',
                    $med['traditional_uses_text'] ?? null,
                    $med['scientific_evidence_text'] ?? null,
                    $med['active_compounds'] ?? null,
                    $med['known_risks'] ?? null,
                    $med['known_interactions'] ?? null,
                    $med['toxic_parts'] ?? null,
                    $med['preparation_risks'] ?? null
                ]);
            }

            // 3. Update Safety Information if provided
            if (isset($data['safety'])) {
                $safe = $data['safety'];
                $stmtSafe = $pdo->prepare("
                    INSERT INTO plant_safety (
                        plant_id, safety_category, primary_warning, toxic_parts,
                        look_alike_species, look_alike_distinction, warning_text
                    ) VALUES (?, ?, ?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE
                        safety_category = VALUES(safety_category),
                        primary_warning = VALUES(primary_warning),
                        toxic_parts = VALUES(toxic_parts),
                        look_alike_species = VALUES(look_alike_species),
                        look_alike_distinction = VALUES(look_alike_distinction),
                        warning_text = VALUES(warning_text)
                ");
                $stmtSafe->execute([
                    $plantId,
                    $safe['safety_category'] ?? 'SAFE_FOR_GENERAL_CONTACT',
                    $safe['primary_warning'] ?? null,
                    $safe['toxic_parts'] ?? null,
                    $safe['look_alike_species'] ?? null,
                    $safe['look_alike_distinction'] ?? null,
                    $safe['warning_text'] ?? null
                ]);
            }

            // 4. Update Conservation Information if provided
            if (isset($data['conservation'])) {
                $cons = $data['conservation'];
                $stmtCons = $pdo->prepare("
                    INSERT INTO plant_conservation (
                        plant_id, iucn_status, denr_status, threatened_status, protected_status, collection_restrictions
                    ) VALUES (?, ?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE
                        iucn_status = VALUES(iucn_status),
                        denr_status = VALUES(denr_status),
                        threatened_status = VALUES(threatened_status),
                        protected_status = VALUES(protected_status),
                        collection_restrictions = VALUES(collection_restrictions)
                ");
                $stmtCons->execute([
                    $plantId,
                    $cons['iucn_status'] ?? 'Least Concern (LC)',
                    $cons['denr_status'] ?? 'Not Listed',
                    $cons['threatened_status'] ?? 'Safe',
                    $cons['protected_status'] ?? 'Not Protected',
                    $cons['collection_restrictions'] ?? null
                ]);
            }

            echo json_encode(['success' => true, 'message' => 'Plant record updated successfully.']);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function deletePlant(string $id): void
    {
        header('Content-Type: application/json');

        try {
            $pdo = Database::getConnection();
            $plantId = (int)$id;

            if (!$plantId) {
                throw new Exception("Valid plant ID required.");
            }

            $stmt = $pdo->prepare("DELETE FROM plants WHERE id = ?");
            $stmt->execute([$plantId]);

            echo json_encode(['success' => true, 'message' => "Plant species #{$plantId} deleted."]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function addPlantAlias(string $plantId): void
    {
        header('Content-Type: application/json');

        try {
            $pdo = Database::getConnection();
            $id = (int)$plantId;
            $name = trim($_POST['name'] ?? '');
            $type = trim($_POST['name_type'] ?? 'local');
            $region = trim($_POST['language_region'] ?? 'Philippines');

            if (empty($name)) {
                throw new Exception("Alias name is required.");
            }

            $stmt = $pdo->prepare("INSERT INTO plant_names (plant_id, name, name_type, language_region, verified_status) VALUES (?, ?, ?, ?, 'verified')");
            $stmt->execute([$id, $name, $type, $region]);

            echo json_encode(['success' => true, 'message' => "Alias '{$name}' added to plant."]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function deletePlantAlias(string $aliasId): void
    {
        header('Content-Type: application/json');

        try {
            $pdo = Database::getConnection();
            $id = (int)$aliasId;

            $stmt = $pdo->prepare("DELETE FROM plant_names WHERE id = ?");
            $stmt->execute([$id]);

            echo json_encode(['success' => true, 'message' => "Alias removed."]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function addPlantImage(string $plantId): void
    {
        header('Content-Type: application/json');

        try {
            $pdo = Database::getConnection();
            $id = (int)$plantId;
            $filePath = trim($_POST['file_path'] ?? '');
            $type = trim($_POST['image_type'] ?? 'leaf');

            if (empty($filePath)) {
                throw new Exception("Image file path or URL is required.");
            }

            $stmt = $pdo->prepare("INSERT INTO plant_images (plant_id, file_path, original_filename, image_type) VALUES (?, ?, ?, ?)");
            $stmt->execute([$id, $filePath, basename($filePath), $type]);

            echo json_encode(['success' => true, 'message' => "Image added to species record."]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function deletePlantImage(string $imageId): void
    {
        header('Content-Type: application/json');

        try {
            $pdo = Database::getConnection();
            $id = (int)$imageId;

            $stmt = $pdo->prepare("DELETE FROM plant_images WHERE id = ?");
            $stmt->execute([$id]);

            echo json_encode(['success' => true, 'message' => "Image removed."]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}

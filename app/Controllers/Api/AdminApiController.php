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

            $plantId = $pdo->lastInsertId();

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

            $stmt = $pdo->prepare("
                UPDATE plants SET
                    primary_common_name = COALESCE(?, primary_common_name),
                    family = COALESCE(?, family),
                    native_status = COALESCE(?, native_status),
                    habitat = COALESCE(?, habitat),
                    philippine_distribution = COALESCE(?, philippine_distribution)
                WHERE id = ?
            ");

            $stmt->execute([
                $data['primary_common_name'] ?? null,
                $data['family'] ?? null,
                $data['native_status'] ?? null,
                $data['habitat'] ?? null,
                $data['philippine_distribution'] ?? null,
                (int)$id
            ]);

            echo json_encode(['success' => true, 'message' => 'Plant updated successfully.']);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}

<?php

namespace App\Controllers\Api;

use App\Helpers\Database;
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

        echo json_encode([
            'stats' => [
                'total_plants' => (int)$plantCount,
                'pending_reviews' => (int)$pendingReviews,
                'total_observations' => (int)$obsCount,
                'total_sources' => (int)$sourceCount
            ]
        ]);
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

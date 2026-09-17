<?php

namespace App\Controllers\Api;

use App\Helpers\Database;
use PDO;

class PlantApiController
{
    public function index(): void
    {
        header('Content-Type: application/json');
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("
            SELECT p.id, p.scientific_name, p.primary_common_name, p.family, p.genus, p.species, p.native_status, p.habitat, p.philippine_distribution,
                   (SELECT file_path FROM plant_images WHERE plant_id = p.id LIMIT 1) as main_image
            FROM plants p
            ORDER BY p.primary_common_name ASC
        ");
        $stmt->execute();
        $plants = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['plants' => $plants]);
    }

    public function search(): void
    {
        header('Content-Type: application/json');
        $query = trim($_GET['q'] ?? '');

        if (empty($query)) {
            echo json_encode(['plants' => []]);
            return;
        }

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            SELECT DISTINCT p.id, p.scientific_name, p.primary_common_name, p.family, p.native_status,
                   (SELECT file_path FROM plant_images WHERE plant_id = p.id LIMIT 1) as main_image
            FROM plants p
            LEFT JOIN plant_names pn ON p.id = pn.plant_id
            WHERE p.scientific_name LIKE ?
               OR p.primary_common_name LIKE ?
               OR pn.name LIKE ?
            LIMIT 25
        ");
        $searchTerm = "%{$query}%";
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['plants' => $results]);
    }

    public function show(string $id): void
    {
        header('Content-Type: application/json');
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("SELECT * FROM plants WHERE id = ?");
        $stmt->execute([(int)$id]);
        $plant = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$plant) {
            http_response_code(404);
            echo json_encode(['error' => 'Plant species not found']);
            return;
        }

        $plantId = $plant['id'];

        // Names
        $stmtN = $pdo->prepare("SELECT id, name, name_type, language_region, verified_status FROM plant_names WHERE plant_id = ?");
        $stmtN->execute([$plantId]);
        $plant['names'] = $stmtN->fetchAll(PDO::FETCH_ASSOC);

        // Images
        $stmtI = $pdo->prepare("SELECT id, file_path, original_filename, image_type, mime_type FROM plant_images WHERE plant_id = ?");
        $stmtI->execute([$plantId]);
        $plant['images'] = $stmtI->fetchAll(PDO::FETCH_ASSOC);

        // Uses
        $stmtU = $pdo->prepare("SELECT id, use_category, title, description, evidence_level FROM plant_uses WHERE plant_id = ?");
        $stmtU->execute([$plantId]);
        $plant['uses'] = $stmtU->fetchAll(PDO::FETCH_ASSOC);

        // Medicinal
        $stmtM = $pdo->prepare("SELECT * FROM plant_medicinal_information WHERE plant_id = ?");
        $stmtM->execute([$plantId]);
        $plant['medicinal'] = $stmtM->fetch(PDO::FETCH_ASSOC);

        // Safety
        $stmtS = $pdo->prepare("SELECT * FROM plant_safety WHERE plant_id = ?");
        $stmtS->execute([$plantId]);
        $plant['safety'] = $stmtS->fetch(PDO::FETCH_ASSOC);

        // Conservation
        $stmtC = $pdo->prepare("SELECT * FROM plant_conservation WHERE plant_id = ?");
        $stmtC->execute([$plantId]);
        $plant['conservation'] = $stmtC->fetch(PDO::FETCH_ASSOC);

        // Sources
        $stmtSrc = $pdo->prepare("SELECT id, source_name, source_url, source_type, fact_type FROM plant_sources WHERE plant_id = ?");
        $stmtSrc->execute([$plantId]);
        $plant['sources'] = $stmtSrc->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['plant' => $plant]);
    }
}

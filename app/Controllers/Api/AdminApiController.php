<?php

namespace App\Controllers\Api;

use App\Helpers\Database;
use PDO;
use Exception;

class AdminApiController
{
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

            // Read JSON input or PUT/PATCH body
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

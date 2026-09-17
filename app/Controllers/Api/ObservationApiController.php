<?php

namespace App\Controllers\Api;

use App\Helpers\Database;
use PDO;
use Exception;

class ObservationApiController
{
    public function index(): void
    {
        header('Content-Type: application/json');
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("
            SELECT o.*, p.scientific_name, p.primary_common_name
            FROM plant_observations o
            LEFT JOIN plants p ON o.plant_id = p.id
            ORDER BY o.created_at DESC
            LIMIT 50
        ");
        $stmt->execute();
        $observations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['observations' => $observations]);
    }

    public function store(): void
    {
        header('Content-Type: application/json');

        try {
            $province = trim($_POST['province'] ?? '');
            $municipality = trim($_POST['municipality'] ?? '');

            if (empty($province) || empty($municipality)) {
                throw new Exception("Province and Municipality are required fields.");
            }

            $obsCode = 'OBS-' . strtoupper(bin2hex(random_bytes(4)));
            $date = $_POST['observation_date'] ?? date('Y-m-d');
            $observer = trim($_POST['observer_name'] ?? 'Anonymous Observer');
            $plantId = !empty($_POST['plant_id']) ? (int)$_POST['plant_id'] : null;
            $identId = !empty($_POST['identification_id']) ? (int)$_POST['identification_id'] : null;

            $pdo = Database::getConnection();
            $stmt = $pdo->prepare("
                INSERT INTO plant_observations (
                    user_id, identification_id, plant_id, observation_code, observer_name, observation_date,
                    province, municipality, barangay, location_description, latitude, longitude,
                    habitat, plant_height_m, estimated_dbh_cm, notes, additional_observations
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $_SESSION['user_id'] ?? null,
                $identId,
                $plantId,
                $obsCode,
                $observer,
                $date,
                $province,
                $municipality,
                $_POST['barangay'] ?? null,
                $_POST['location_description'] ?? null,
                !empty($_POST['latitude']) ? (float)$_POST['latitude'] : null,
                !empty($_POST['longitude']) ? (float)$_POST['longitude'] : null,
                $_POST['habitat'] ?? null,
                !empty($_POST['plant_height_m']) ? (float)$_POST['plant_height_m'] : null,
                !empty($_POST['estimated_dbh_cm']) ? (float)$_POST['estimated_dbh_cm'] : null,
                $_POST['notes'] ?? null,
                $_POST['additional_observations'] ?? null
            ]);

            $obsId = $pdo->lastInsertId();

            echo json_encode([
                'success' => true,
                'observation_id' => $obsId,
                'observation_code' => $obsCode,
                'message' => 'Plant field observation successfully recorded.'
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function export(): void
    {
        $format = strtolower($_GET['format'] ?? 'json');
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("
            SELECT o.observation_code, o.observation_date, o.observer_name, o.province, o.municipality, o.barangay,
                   o.location_description, o.latitude, o.longitude, o.habitat, o.plant_height_m, o.estimated_dbh_cm, o.notes,
                   p.scientific_name, p.primary_common_name
            FROM plant_observations o
            LEFT JOIN plants p ON o.plant_id = p.id
            ORDER BY o.created_at DESC
        ");
        $stmt->execute();
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($format === 'csv') {
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="philippine_plant_observations.csv"');
            $out = fopen('php://output', 'w');
            fputcsv($out, array_keys($records[0] ?? ['Observation Code', 'Date', 'Observer', 'Province', 'Municipality', 'Barangay', 'Location', 'Latitude', 'Longitude', 'Habitat', 'Height (m)', 'DBH (cm)', 'Notes', 'Scientific Name', 'Common Name']));
            foreach ($records as $row) {
                fputcsv($out, $row);
            }
            fclose($out);
            exit;
        }

        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="philippine_plant_observations.json"');
        echo json_encode(['observations' => $records], JSON_PRETTY_PRINT);
    }
}

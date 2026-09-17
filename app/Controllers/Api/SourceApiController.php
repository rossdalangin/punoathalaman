<?php

namespace App\Controllers\Api;

use App\Helpers\Database;
use PDO;

class SourceApiController
{
    public function index(): void
    {
        header('Content-Type: application/json');
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("
            SELECT s.*, p.scientific_name, p.primary_common_name
            FROM plant_sources s
            LEFT JOIN plants p ON s.plant_id = p.id
            ORDER BY s.source_name ASC
        ");
        $stmt->execute();
        $sources = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['sources' => $sources]);
    }
}

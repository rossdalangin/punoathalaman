<?php

namespace App\Controllers\Api;

use App\Helpers\Database;
use Exception;

class ExpertReviewApiController
{
    public function store(): void
    {
        header('Content-Type: application/json');

        try {
            $identificationId = (int)($_POST['identification_id'] ?? 0);
            $userNotes = trim($_POST['notes'] ?? '');

            if (!$identificationId) {
                throw new Exception("Identification ID is required.");
            }

            $pdo = Database::getConnection();
            $stmt = $pdo->prepare("
                UPDATE plant_identifications
                SET status = 'PENDING_REVIEW', expert_notes = ?
                WHERE id = ?
            ");
            $stmt->execute(["User requested review: " . $userNotes, $identificationId]);

            echo json_encode([
                'success' => true,
                'message' => 'Your identification request has been submitted to Philippine botanists & foresters for expert review.'
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}

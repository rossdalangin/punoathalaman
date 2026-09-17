<?php

namespace App\Controllers\Api;

use App\AI\AIPlantIdentifierFactory;
use App\Helpers\Database;
use App\Services\UploadService;
use Exception;

class IdentifyController
{
    private UploadService $uploadService;

    public function __construct()
    {
        $this->uploadService = new UploadService();
    }

    public function identify(): void
    {
        header('Content-Type: application/json');

        try {
            if (empty($_FILES['image'])) {
                throw new Exception("Please upload a plant photograph.");
            }

            $upload = $this->uploadService->uploadSingle($_FILES['image'], $_POST['image_type'] ?? 'leaf');
            $imagePaths = [$upload['absolute_path']];

            // Metadata from user follow-up questions
            $metadata = [
                'location_found' => $_POST['location_found'] ?? null,
                'plant_height' => $_POST['plant_height'] ?? null,
                'has_flowers' => $_POST['has_flowers'] ?? null,
                'has_fruit' => $_POST['has_fruit'] ?? null,
                'milky_sap' => $_POST['milky_sap'] ?? null,
                'leaf_arrangement' => $_POST['leaf_arrangement'] ?? null,
                'has_thorns' => $_POST['has_thorns'] ?? null,
                'province' => $_POST['province'] ?? null,
            ];

            // Run AI identifier
            $identifier = AIPlantIdentifierFactory::create();
            $result = $identifier->identifyPlant($imagePaths, array_filter($metadata));

            // Save to database
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare("INSERT INTO identification_requests (session_id, user_answers_json, image_quality_rating, quality_feedback) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                session_id(),
                json_encode($metadata),
                $upload['quality']['rating'],
                $upload['quality']['instructions']
            ]);
            $requestId = $pdo->lastInsertId();

            // Save image record
            $stmtImg = $pdo->prepare("INSERT INTO plant_images (request_id, file_path, original_filename, image_type, mime_type, file_size) VALUES (?, ?, ?, ?, ?, ?)");
            $stmtImg->execute([
                $requestId,
                $upload['file_path'],
                $upload['original_filename'],
                $upload['image_type'],
                $upload['mime_type'],
                $upload['file_size']
            ]);

            // Save identification record
            $primaryPlantId = $result->primaryCandidate['id'] ?? null;
            $stmtIdent = $pdo->prepare("INSERT INTO plant_identifications (request_id, session_id, primary_plant_id, confidence_score, confidence_level, reasoning_summary, raw_ai_response, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'AI_IDENTIFIED')");
            $stmtIdent->execute([
                $requestId,
                session_id(),
                $primaryPlantId,
                $result->confidenceScore,
                $result->confidenceLevel,
                $result->reasoningSummary,
                json_encode($result->rawOutput)
            ]);
            $identificationId = $pdo->lastInsertId();

            $responseData = $result->toArray();
            $responseData['identification_id'] = $identificationId;
            $responseData['request_id'] = $requestId;
            $responseData['uploaded_images'] = [$upload['file_path']];
            $responseData['quality_analysis'] = $upload['quality'];

            echo json_encode($responseData);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'error' => $e->getMessage(),
                'status' => 'error'
            ]);
        }
    }

    public function identifyMultiple(): void
    {
        header('Content-Type: application/json');

        try {
            if (empty($_FILES['images'])) {
                throw new Exception("Please select at least two photographs of the plant.");
            }

            $types = $_POST['image_types'] ?? [];
            $uploads = $this->uploadService->uploadMultiple($_FILES['images'], $types);

            $absPaths = array_map(fn($u) => $u['absolute_path'], $uploads);
            $webPaths = array_map(fn($u) => $u['file_path'], $uploads);

            $metadata = [
                'location_found' => $_POST['location_found'] ?? null,
                'province' => $_POST['province'] ?? null,
                'multi_photo' => true,
                'photo_count' => count($uploads)
            ];

            $identifier = AIPlantIdentifierFactory::create();
            $result = $identifier->identifyPlant($absPaths, array_filter($metadata));

            // DB saving
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare("INSERT INTO identification_requests (session_id, user_answers_json, image_quality_rating) VALUES (?, ?, 'GOOD')");
            $stmt->execute([session_id(), json_encode($metadata)]);
            $requestId = $pdo->lastInsertId();

            foreach ($uploads as $upload) {
                $stmtImg = $pdo->prepare("INSERT INTO plant_images (request_id, file_path, original_filename, image_type, mime_type, file_size) VALUES (?, ?, ?, ?, ?, ?)");
                $stmtImg->execute([
                    $requestId,
                    $upload['file_path'],
                    $upload['original_filename'],
                    $upload['image_type'],
                    $upload['mime_type'],
                    $upload['file_size']
                ]);
            }

            $primaryPlantId = $result->primaryCandidate['id'] ?? null;
            $stmtIdent = $pdo->prepare("INSERT INTO plant_identifications (request_id, session_id, primary_plant_id, confidence_score, confidence_level, reasoning_summary, raw_ai_response, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'AI_IDENTIFIED')");
            $stmtIdent->execute([
                $requestId,
                session_id(),
                $primaryPlantId,
                $result->confidenceScore,
                $result->confidenceLevel,
                $result->reasoningSummary,
                json_encode($result->rawOutput)
            ]);
            $identificationId = $pdo->lastInsertId();

            $responseData = $result->toArray();
            $responseData['identification_id'] = $identificationId;
            $responseData['request_id'] = $requestId;
            $responseData['uploaded_images'] = $webPaths;

            echo json_encode($responseData);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}

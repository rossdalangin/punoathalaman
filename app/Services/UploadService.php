<?php

namespace App\Services;

use App\Helpers\Config;
use Exception;

class UploadService
{
    private string $uploadDir;
    private array $allowedMimeTypes;
    private int $maxSizeBytes;

    public function __construct()
    {
        $storageDir = Config::get('STORAGE_PATH', 'storage/uploads');
        $this->uploadDir = __DIR__ . '/../../' . rtrim($storageDir, '/') . '/';

        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }

        $this->allowedMimeTypes = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
        ];

        $maxMb = (int)Config::get('MAX_UPLOAD_SIZE_MB', 10);
        $this->maxSizeBytes = $maxMb * 1024 * 1024;
    }

    /**
     * Handles single uploaded file from $_FILES.
     */
    public function uploadSingle(array $fileData, string $imageType = 'leaf'): array
    {
        if (!isset($fileData['tmp_name']) || $fileData['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("File upload failed or no file uploaded (Code: " . ($fileData['error'] ?? 'unknown') . ")");
        }

        if ($fileData['size'] > $this->maxSizeBytes) {
            throw new Exception("Uploaded file exceeds the maximum size limit of " . Config::get('MAX_UPLOAD_SIZE_MB', 10) . "MB.");
        }

        // Validate MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $fileData['tmp_name']);
        finfo_close($finfo);

        if (!array_key_exists($mime, $this->allowedMimeTypes)) {
            throw new Exception("Invalid file type: {$mime}. Only JPG, PNG, and WEBP image files are allowed.");
        }

        $ext = $this->allowedMimeTypes[$mime];
        $randomName = bin2hex(random_bytes(16)) . '.' . $ext;
        $targetPath = $this->uploadDir . $randomName;

        if (!move_uploaded_file($fileData['tmp_name'], $targetPath)) {
            throw new Exception("Failed to save uploaded image to storage directory.");
        }

        // Relative path for web access and database records
        $relativePath = 'storage/uploads/' . $randomName;

        // Perform quality check
        $quality = ImageQualityAnalyzer::analyzeQuality($targetPath);

        return [
            'file_path' => $relativePath,
            'absolute_path' => $targetPath,
            'original_filename' => htmlspecialchars($fileData['name'], ENT_QUOTES, 'UTF-8'),
            'mime_type' => $mime,
            'file_size' => $fileData['size'],
            'image_type' => $imageType,
            'quality' => $quality
        ];
    }

    /**
     * Handles multiple image uploads.
     */
    public function uploadMultiple(array $filesData, array $imageTypes = []): array
    {
        $uploaded = [];

        // Re-structure PHP $_FILES multi array if needed
        if (isset($filesData['name']) && is_array($filesData['name'])) {
            $count = count($filesData['name']);
            for ($i = 0; $i < $count; $i++) {
                if ($filesData['error'][$i] === UPLOAD_ERR_OK) {
                    $single = [
                        'name' => $filesData['name'][$i],
                        'type' => $filesData['type'][$i],
                        'tmp_name' => $filesData['tmp_name'][$i],
                        'error' => $filesData['error'][$i],
                        'size' => $filesData['size'][$i],
                    ];
                    $type = $imageTypes[$i] ?? 'leaf';
                    $uploaded[] = $this->uploadSingle($single, $type);
                }
            }
        }

        if (empty($uploaded)) {
            throw new Exception("No valid image files were successfully uploaded.");
        }

        return $uploaded;
    }
}

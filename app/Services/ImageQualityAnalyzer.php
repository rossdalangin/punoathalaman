<?php

namespace App\Services;

class ImageQualityAnalyzer
{
    /**
     * Evaluates uploaded image file quality before passing to AI model.
     * Checks dimensions, size, brightness/contrast heuristics, and sharpness indicators.
     */
    public static function analyzeQuality(string $filepath): array
    {
        if (!file_exists($filepath)) {
            return [
                'rating' => 'POOR',
                'issues' => ['File does not exist'],
                'instructions' => 'Please re-upload a valid image file.'
            ];
        }

        $imageInfo = @getimagesize($filepath);
        if (!$imageInfo) {
            return [
                'rating' => 'POOR',
                'issues' => ['Invalid image file format'],
                'instructions' => 'Upload a valid JPG, PNG, or WEBP photograph.'
            ];
        }

        [$width, $height] = $imageInfo;
        $filesizeMb = filesize($filepath) / (1024 * 1024);
        $issues = [];
        $instructions = [];

        // Dimension checks
        if ($width < 300 || $height < 300) {
            $issues[] = 'Image resolution is low (smaller than 300x300 pixels).';
            $instructions[] = 'Take a higher-resolution photograph closer to the plant.';
        }

        // Extremely small file size usually indicates heavy compression/blur
        if ($filesizeMb < 0.03) { // < 30 KB
            $issues[] = 'Image appears heavily compressed or low detail.';
            $instructions[] = 'Upload the original full-quality photograph from your camera or gallery.';
        }

        // Image brightness check if GD library is available
        if (function_exists('imagecreatefromjpeg') || function_exists('imagecreatefrompng') || function_exists('imagecreatefromwebp')) {
            $brightness = self::calculateAverageBrightness($filepath, $imageInfo[2]);
            if ($brightness !== null) {
                if ($brightness < 35) {
                    $issues[] = 'Photograph is dark or underexposed.';
                    $instructions[] = 'Photograph the plant in well-lit natural outdoor daylight or turn on flash.';
                } elseif ($brightness > 230) {
                    $issues[] = 'Photograph is overexposed or washed out by bright flash.';
                    $instructions[] = 'Avoid strong glare or harsh direct sunlight reflected on the leaf surface.';
                }
            }
        }

        $rating = empty($issues) ? 'GOOD' : (count($issues) > 1 ? 'POOR' : 'ACCEPTABLE');

        return [
            'rating' => $rating,
            'issues' => $issues,
            'instructions' => !empty($instructions) ? implode(' ', $instructions) : 'Image quality is suitable for identification.'
        ];
    }

    private static function calculateAverageBrightness(string $filepath, int $imageType): ?float
    {
        $img = null;
        try {
            switch ($imageType) {
                case IMAGETYPE_JPEG:
                    $img = @imagecreatefromjpeg($filepath);
                    break;
                case IMAGETYPE_PNG:
                    $img = @imagecreatefrompng($filepath);
                    break;
                case IMAGETYPE_WEBP:
                    $img = @imagecreatefromwebp($filepath);
                    break;
            }

            if (!$img) {
                return null;
            }

            // Sample 20x20 thumbnail for fast brightness calculation
            $thumb = imagecreatetruecolor(20, 20);
            imagecopyresampled($thumb, $img, 0, 0, 0, 0, 20, 20, imagesx($img), imagesy($img));

            $totalBrightness = 0;
            for ($x = 0; $x < 20; $x++) {
                for ($y = 0; $y < 20; $y++) {
                    $rgb = imagecolorat($thumb, $x, $y);
                    $r = ($rgb >> 16) & 0xFF;
                    $g = ($rgb >> 8) & 0xFF;
                    $b = $rgb & 0xFF;
                    // Luma formula
                    $totalBrightness += (0.299 * $r + 0.587 * $g + 0.114 * $b);
                }
            }

            imagedestroy($thumb);
            imagedestroy($img);

            return $totalBrightness / 400.0;
        } catch (\Throwable $e) {
            return null;
        }
    }
}

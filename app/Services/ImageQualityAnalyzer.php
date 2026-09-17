<?php

namespace App\Services;

class ImageQualityAnalyzer
{
    /**
     * Evaluates uploaded image file quality before passing to AI model.
     * Checks dimensions, size, brightness/contrast heuristics, and screenshot indicators.
     */
    public static function analyzeQuality(string $filepath): array
    {
        if (!file_exists($filepath)) {
            return [
                'rating' => 'POOR',
                'is_screenshot' => false,
                'issues' => ['File does not exist'],
                'instructions' => 'Please re-upload a valid image file.'
            ];
        }

        $imageInfo = @getimagesize($filepath);
        if (!$imageInfo) {
            return [
                'rating' => 'POOR',
                'is_screenshot' => false,
                'issues' => ['Invalid image file format'],
                'instructions' => 'Upload a valid JPG, PNG, or WEBP photograph or screenshot.'
            ];
        }

        [$width, $height] = $imageInfo;
        $filesizeMb = filesize($filepath) / (1024 * 1024);
        $filenameLower = strtolower(basename($filepath));

        $isScreenshot = str_contains($filenameLower, 'screenshot') || str_contains($filenameLower, 'screen') || str_contains($filenameLower, 'capture');
        $issues = [];
        $instructions = [];

        // Aspect ratio check for typical mobile phone screenshots (e.g. 19.5:9, 16:9, 20:9)
        $aspectRatio = $height > 0 ? $width / $height : 1.0;
        if ($aspectRatio < 0.52 || $aspectRatio > 2.1) {
            $isScreenshot = true;
        }

        if ($isScreenshot) {
            $instructions[] = 'Screenshot detected. AI vision will isolate the plant/tree subject from screen borders and UI text.';
        }

        // Dimension checks
        if ($width < 300 || $height < 300) {
            $issues[] = 'Image resolution is low (smaller than 300x300 pixels).';
            $instructions[] = 'Take a higher-resolution photograph or screenshot closer to the leaf or tree.';
        }

        // Extremely small file size usually indicates heavy compression
        if ($filesizeMb < 0.02) { // < 20 KB
            $issues[] = 'Image appears heavily compressed or low detail.';
            $instructions[] = 'Upload the original full-quality photograph from your camera or gallery.';
        }

        // Image brightness check if GD library is available
        if (function_exists('imagecreatefromjpeg') || function_exists('imagecreatefrompng') || function_exists('imagecreatefromwebp')) {
            $brightness = self::calculateAverageBrightness($filepath, $imageInfo[2]);
            if ($brightness !== null) {
                if ($brightness < 30) {
                    $issues[] = 'Photograph is dark or underexposed.';
                    $instructions[] = 'Photograph the plant or tree in well-lit natural outdoor daylight.';
                } elseif ($brightness > 235) {
                    $issues[] = 'Photograph is overexposed or washed out by bright flash.';
                    $instructions[] = 'Avoid strong glare or harsh direct sunlight reflected on the leaf surface.';
                }
            }
        }

        $rating = empty($issues) ? 'GOOD' : (count($issues) > 1 ? 'POOR' : 'ACCEPTABLE');

        return [
            'rating' => $rating,
            'is_screenshot' => $isScreenshot,
            'issues' => $issues,
            'instructions' => !empty($instructions) ? implode(' ', $instructions) : 'Image quality is suitable for botanical vision analysis.'
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

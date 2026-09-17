<?php

namespace App\AI;

interface AIPlantIdentifierInterface
{
    /**
     * Identifies a plant from one or multiple uploaded image paths.
     *
     * @param array $imagePaths List of local file paths for analysis.
     * @param array $metadata Optional metadata (answers to follow-up questions, user location, etc.)
     * @return AIResult Structured result object.
     */
    public function identifyPlant(array $imagePaths, array $metadata = []): AIResult;
}

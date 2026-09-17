<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\AI\MockPlantIdentifier;
use App\Helpers\Config;

class PlantIdentifierTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        Config::loadEnv(__DIR__ . '/../.env');
    }

    public function testMockIdentificationForBanaba(): void
    {
        $identifier = new MockPlantIdentifier();
        $result = $identifier->identifyPlant(['storage/uploads/banaba_leaf.jpg']);

        $this->assertEquals('identified', $result->status);
        $this->assertEquals('Lagerstroemia speciosa', $result->primaryCandidate['scientific_name']);
        $this->assertGreaterThanOrEqual(80.0, $result->confidenceScore);
        $this->assertEquals('High', $result->confidenceLevel);
    }

    public function testMockIdentificationForSambong(): void
    {
        $identifier = new MockPlantIdentifier();
        $result = $identifier->identifyPlant(['storage/uploads/sambong_leaf.jpg']);

        $this->assertEquals('identified', $result->status);
        $this->assertEquals('Blumea balsamifera', $result->primaryCandidate['scientific_name']);
        $this->assertEquals('Sambong', $result->primaryCandidate['common_name']);
        $this->assertGreaterThanOrEqual(85.0, $result->confidenceScore);
    }

    public function testMockIdentificationForPoisonousPlant(): void
    {
        $identifier = new MockPlantIdentifier();
        $result = $identifier->identifyPlant(['storage/uploads/poison_jatropha.jpg']);

        $this->assertEquals('Jatropha curcas', $result->primaryCandidate['scientific_name']);
        $this->assertEquals('KNOWN_POISONOUS_PLANT', $result->safety['safety_category']);
        $this->assertNotEmpty($result->safety['warning_text']);
    }

    public function testBlurryImageQualityDetection(): void
    {
        $identifier = new MockPlantIdentifier();
        $result = $identifier->identifyPlant(['storage/uploads/blurry_leaf.jpg']);

        $this->assertEquals('low_confidence', $result->status);
        $this->assertEquals('Low', $result->confidenceLevel);
    }

    public function testNonPlantImageDetection(): void
    {
        $identifier = new MockPlantIdentifier();
        $result = $identifier->identifyPlant(['storage/uploads/nonplant_animal.jpg']);

        $this->assertEquals('non_plant', $result->status);
        $this->assertNull($result->primaryCandidate);
    }

    public function testMultiPhotoConfidenceBoost(): void
    {
        $identifier = new MockPlantIdentifier();
        $resultSingle = $identifier->identifyPlant(['storage/uploads/sambong_leaf.jpg']);
        $resultMulti = $identifier->identifyPlant(['storage/uploads/sambong_leaf.jpg', 'storage/uploads/bark.jpg', 'storage/uploads/flower.jpg']);

        $this->assertGreaterThan($resultSingle->confidenceScore, $resultMulti->confidenceScore);
    }
}

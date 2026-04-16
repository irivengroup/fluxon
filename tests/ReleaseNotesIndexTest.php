<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Tests;

use PHPUnit\Framework\TestCase;

final class ReleaseNotesIndexTest extends TestCase
{
    public function testReleaseNotesIndexPointsToReleaseDirectoryOnly(): void
    {
        $content = file_get_contents(__DIR__ . '/../RELEASE_NOTES.md');
        self::assertIsString($content);
        self::assertStringNotContainsString('releases.d/releases.d/', $content);
        self::assertStringContainsString('releases.d/RELEASE_NOTES_V4.4.0.md', $content);
    }

    public function testValidationManifestIndexPointsToValidationDirectoryOnly(): void
    {
        $content = file_get_contents(__DIR__ . '/../VALIDATION_MANIFEST.md');
        self::assertIsString($content);
        self::assertStringNotContainsString('validations.d/validations.d/', $content);
        self::assertStringContainsString('validations.d/VALIDATION_MANIFEST_V4.4.0.md', $content);
    }
}

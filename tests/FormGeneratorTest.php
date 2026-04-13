<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Tests;

use Iriven\PhpFormGenerator\FormGenerator;
use Iriven\PhpFormGenerator\Infrastructure\ArrayRequestDataProvider;
use PHPUnit\Framework\TestCase;

final class FormGeneratorTest extends TestCase
{
    public function test_prefills_values(): void
    {
        $form = new FormGenerator(new ArrayRequestDataProvider([], ['email' => 'john@example.com'], 'POST'));
        $html = $form->open(['method' => 'post'])->addEmail('Email')->close();
        self::assertStringContainsString('value="john@example.com"', $html);
    }

    public function test_renders_fieldset(): void
    {
        $form = new FormGenerator();
        $html = $form->open()->addFieldset(['legend' => 'Profile'])->addText('Name')->endFieldset()->close();
        self::assertStringContainsString('<fieldset', $html);
        self::assertStringContainsString('<legend', $html);
    }

    public function test_switches_to_multipart(): void
    {
        $form = new FormGenerator();
        $html = $form->open()->addFile('Document')->close();
        self::assertStringContainsString('multipart/form-data', $html);
    }
}

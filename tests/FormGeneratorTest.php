<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Tests;

use Iriven\PhpFormGenerator\FormGenerator;
use Iriven\PhpFormGenerator\Infrastructure\Request\ArrayRequestDataProvider;
use PHPUnit\Framework\TestCase;

final class FormGeneratorTest extends TestCase
{
    public function testItRendersSimpleForm(): void
    {
        $form = new FormGenerator(new ArrayRequestDataProvider(method: 'POST', post: ['name' => 'Ada']));
        $html = $form
            ->open(['method' => 'post'])
            ->addText('Name')
            ->addSubmit('Save')
            ->close();

        self::assertStringContainsString('<form', $html);
        self::assertStringContainsString('value="Ada"', $html);
        self::assertStringContainsString('type="submit"', $html);
    }

    public function testItRendersSelectAndFieldset(): void
    {
        $form = new FormGenerator();
        $html = $form
            ->open()
            ->addFieldset(['legend' => 'Profile'])
            ->addSelect('Role', ['admin' => 'Admin', 'user' => 'User'])
            ->endFieldset()
            ->close();

        self::assertStringContainsString('<fieldset', $html);
        self::assertStringContainsString('<legend>Profile</legend>', $html);
        self::assertStringContainsString('<select', $html);
    }

    public function testItForcesMultipartForFiles(): void
    {
        $form = new FormGenerator();
        $html = $form
            ->open()
            ->addFile('Avatar')
            ->close();

        self::assertStringContainsString('enctype="multipart/form-data"', $html);
        self::assertStringContainsString('type="file"', $html);
    }
}

<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Tests;

use Iriven\PhpFormGenerator\Application\FormGenerator;
use Iriven\PhpFormGenerator\Infrastructure\Http\ArrayRequest;
use Iriven\PhpFormGenerator\Presentation\Html\HtmlRenderer;
use PHPUnit\Framework\TestCase;

final class MediaFieldMimeConstraintTest extends TestCase
{
    public function testMediaFieldsRenderAcceptAttributeAutomatically(): void
    {
        $form = (new FormGenerator('media'))
            ->open(['method' => 'POST'])
            ->addAudio('audio')
            ->addImage('image')
            ->addVideo('video')
            ->getForm();

        $html = (new HtmlRenderer())->renderForm($form->createView());

        self::assertStringContainsString('name="media[audio]"', $html);
        self::assertStringContainsString('accept="audio/*"', $html);
        self::assertStringContainsString('name="media[image]"', $html);
        self::assertStringContainsString('accept="image/*"', $html);
        self::assertStringContainsString('name="media[video]"', $html);
        self::assertStringContainsString('accept="video/*"', $html);
    }

    public function testMediaFieldsRejectUnexpectedMimeTypes(): void
    {
        $form = (new FormGenerator('media'))
            ->open(['method' => 'POST'])
            ->addAudio('audio')
            ->addImage('image')
            ->addVideo('video')
            ->getForm();

        $form->handleRequest(new ArrayRequest('POST', [
            'media' => [
                'audio' => ['name' => 'photo.png', 'type' => 'image/png'],
                'image' => ['name' => 'song.mp3', 'type' => 'audio/mpeg'],
                'video' => ['name' => 'track.mp3', 'type' => 'audio/mpeg'],
            ],
        ]));

        self::assertTrue($form->isSubmitted());
        self::assertFalse($form->isValid());
        $errors = $form->getErrors();
        self::assertArrayHasKey('audio', $errors);
        self::assertArrayHasKey('image', $errors);
        self::assertArrayHasKey('video', $errors);
    }
}

<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Tests\Fixtures\Hook;

use Iriven\PhpFormGenerator\Domain\Contract\FormHookInterface;
use Iriven\PhpFormGenerator\Domain\Form\Form;
use RuntimeException;

final class ThrowingHook implements FormHookInterface
{
    public static function getName(): string
    {
        return 'post_submit';
    }

    public function __invoke(Form $form, array $context = []): void
    {
        throw new RuntimeException('Boom');
    }
}

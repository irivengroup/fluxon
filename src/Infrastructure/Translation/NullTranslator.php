<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Infrastructure\Translation;

final class NullTranslator
{
    public function trans(string $message): string
    {
        return $message;
    }
}

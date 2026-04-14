<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Contract;

interface ValidationGroupAwareInterface
{
    /**
     * @return array<int, string>
     */
    public function groups(): array;
}

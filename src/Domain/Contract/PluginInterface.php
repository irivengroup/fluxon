<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Contract;

/**
 * @api
 */
interface PluginInterface
{
    /**
     * @return array<int, class-string>
     */
    public function registerFieldTypes(): array;

    /**
     * @return array<int, class-string>
     */
    public function registerFormTypes(): array;

    /**
     * @return array<int, object>
     */
    public function registerExtensions(): array;
}

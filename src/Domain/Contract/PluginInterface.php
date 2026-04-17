<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Contract;

use Iriven\PhpFormGenerator\Infrastructure\Registry\PluginRegistry;

/**
 * @api
 */
interface PluginInterface
{
    public function register(PluginRegistry $registry): void;
}

<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Tests\Fixtures\Plugin;

use Iriven\PhpFormGenerator\Domain\Contract\FieldTypeRegistryInterface;
use Iriven\PhpFormGenerator\Domain\Contract\FormTypeRegistryInterface;
use Iriven\PhpFormGenerator\Domain\Contract\PluginInterface;
use Iriven\PhpFormGenerator\Infrastructure\Extension\ExtensionRegistry;

final class EmptyPlugin implements PluginInterface
{
    public function registerFieldTypes(FieldTypeRegistryInterface $registry): void
    {
    }

    public function registerFormTypes(FormTypeRegistryInterface $registry): void
    {
    }

    public function registerExtensions(ExtensionRegistry $registry): void
    {
    }

    public function register(\Iriven\PhpFormGenerator\Infrastructure\Registry\PluginRegistry $registry): void
    {
        $registry->register($this);
    }
}

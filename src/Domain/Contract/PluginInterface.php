<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Contract;

/**
 * @api
 */
interface PluginInterface
{
    public function registerFieldTypes(FieldTypeRegistryInterface $registry): void;

    public function registerFormTypes(FormTypeRegistryInterface $registry): void;

    public function registerExtensions(ExtensionRegistryInterface $registry): void;
}

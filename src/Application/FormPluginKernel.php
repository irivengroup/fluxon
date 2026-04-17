<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Application;

use Iriven\PhpFormGenerator\Domain\Contract\PluginInterface;
use Iriven\PhpFormGenerator\Infrastructure\Extension\ExtensionRegistry;
use Iriven\PhpFormGenerator\Infrastructure\Registry\InMemoryFieldTypeRegistry;
use Iriven\PhpFormGenerator\Infrastructure\Registry\InMemoryFormTypeRegistry;
use Iriven\PhpFormGenerator\Infrastructure\Registry\PluginRegistry;

final class FormPluginKernel
{
    private PluginRegistry $registry;

    public function __construct(?PluginRegistry $registry = null)
    {
        $this->registry = $registry ?? new PluginRegistry(
            new InMemoryFieldTypeRegistry(),
            new InMemoryFormTypeRegistry(),
            new ExtensionRegistry(),
        );
    }

    public function register(PluginInterface $plugin): self
    {
        $this->registry->registerPlugin($plugin);

        return $this;
    }

    public function fieldTypes(): InMemoryFieldTypeRegistry
    {
        return $this->registry->fieldTypes();
    }

    public function formTypes(): InMemoryFormTypeRegistry
    {
        return $this->registry->formTypes();
    }

    public function extensions(): ExtensionRegistry
    {
        return $this->registry->extensions();
    }

    public function registry(): PluginRegistry
    {
        return $this->registry;
    }
}

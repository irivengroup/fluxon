<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Application;

use Iriven\PhpFormGenerator\Domain\Contract\PluginInterface;
use Iriven\PhpFormGenerator\Infrastructure\Extension\ExtensionRegistry;
use Iriven\PhpFormGenerator\Infrastructure\Registry\BuiltinRegistries;
use Iriven\PhpFormGenerator\Infrastructure\Registry\InMemoryFieldTypeRegistry;
use Iriven\PhpFormGenerator\Infrastructure\Registry\InMemoryFormTypeRegistry;
use Iriven\PhpFormGenerator\Infrastructure\Registry\PluginRegistry;
use Iriven\PhpFormGenerator\Infrastructure\Type\TypeResolver;

final class FormPluginKernel
{
    private PluginRegistry $plugins;

    public function __construct(?ExtensionRegistry $extensionRegistry = null)
    {
        $this->plugins = new PluginRegistry(
            BuiltinRegistries::fieldTypes(),
            BuiltinRegistries::formTypes(),
            $extensionRegistry ?? new ExtensionRegistry(),
        );

        $this->bootRuntime();
    }

    public function register(PluginInterface $plugin): self
    {
        $this->plugins->registerPlugin($plugin);
        $this->bootRuntime();

        return $this;
    }

    public function plugins(): PluginRegistry
    {
        return $this->plugins;
    }

    public function fieldTypes(): InMemoryFieldTypeRegistry
    {
        return $this->plugins->fieldTypes();
    }

    public function formTypes(): InMemoryFormTypeRegistry
    {
        return $this->plugins->formTypes();
    }

    public function extensions(): ExtensionRegistry
    {
        return $this->plugins->extensions();
    }

    private function bootRuntime(): void
    {
        TypeResolver::useRegistries($this->fieldTypes(), $this->formTypes());
    }
}

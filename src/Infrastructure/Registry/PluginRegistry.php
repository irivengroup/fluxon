<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Infrastructure\Registry;

use Iriven\PhpFormGenerator\Domain\Contract\PluginInterface;

final class PluginRegistry
{
    /** @var array<int, class-string> */
    private array $fieldTypes = [];

    /** @var array<int, class-string> */
    private array $formTypes = [];

    /** @var array<int, object> */
    private array $extensions = [];

    public function add(PluginInterface $plugin): void
    {
        foreach ($plugin->registerFieldTypes() as $fieldType) {
            $this->fieldTypes[] = $fieldType;
        }

        foreach ($plugin->registerFormTypes() as $formType) {
            $this->formTypes[] = $formType;
        }

        foreach ($plugin->registerExtensions() as $extension) {
            $this->extensions[] = $extension;
        }
    }

    public function register(PluginInterface $plugin): void
    {
        $this->add($plugin);
    }

    /**
     * @return array<int, class-string>
     */
    public function fieldTypes(): array
    {
        return $this->fieldTypes;
    }

    /**
     * @return array<int, class-string>
     */
    public function formTypes(): array
    {
        return $this->formTypes;
    }

    /**
     * @return array<int, object>
     */
    public function extensions(): array
    {
        return $this->extensions;
    }
}

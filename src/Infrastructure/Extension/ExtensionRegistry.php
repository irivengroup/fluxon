<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Infrastructure\Extension;

use Iriven\PhpFormGenerator\Domain\Contract\FieldTypeExtensionInterface;
use Iriven\PhpFormGenerator\Domain\Contract\FormExtensionInterface;

final class ExtensionRegistry
{
    /** @var array<int, FieldTypeExtensionInterface> */
    private array $fieldExtensions = [];

    /** @var array<int, FormExtensionInterface> */
    private array $formExtensions = [];

    public function addFieldTypeExtension(FieldTypeExtensionInterface $extension): void
    {
        $this->fieldExtensions[] = $extension;
    }

    public function addFormExtension(FormExtensionInterface $extension): void
    {
        $this->formExtensions[] = $extension;
    }

    /**
     * @param string $typeClass
     * @return array<int, FieldTypeExtensionInterface>
     */
    public function fieldExtensionsFor(string $typeClass): array
    {
        return array_values(array_filter(
            $this->fieldExtensions,
            static fn (FieldTypeExtensionInterface $extension): bool => $extension::getExtendedType() === $typeClass
        ));
    }

    /**
     * @return array<int, FormExtensionInterface>
     */
    public function formExtensions(): array
    {
        return $this->formExtensions;
    }
}

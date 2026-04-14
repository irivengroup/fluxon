<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Application;

use Iriven\PhpFormGenerator\Domain\Contract\CsrfManagerInterface;
use Iriven\PhpFormGenerator\Domain\Contract\FormTypeInterface;
use Iriven\PhpFormGenerator\Domain\Form\Form;
use Iriven\PhpFormGenerator\Domain\Form\FormBuilder;
use Iriven\PhpFormGenerator\Infrastructure\Security\NullCsrfManager;

final class FormFactory
{
    public function __construct(private readonly ?CsrfManagerInterface $csrfManager = null)
    {
    }

    public function createBuilder(string $name = 'form', mixed $data = null, array $options = []): FormBuilder
    {
        $options['csrf_manager'] = $options['csrf_manager'] ?? $this->csrfManager ?? new NullCsrfManager();

        return new FormBuilder($name, $data, $options);
    }

    /** @param class-string<FormTypeInterface> $typeClass */
    public function create(string $typeClass, mixed $data = null, array $options = []): Form
    {
        $options['csrf_manager'] = $options['csrf_manager'] ?? $this->csrfManager ?? new NullCsrfManager();
        $builder = new FormBuilder($options['name'] ?? 'form', $data, $options);
        $type = new $typeClass();
        $resolved = $type->configureOptions($options);
        $type->buildForm($builder, $resolved + $options);

        return $builder->getForm();
    }
}

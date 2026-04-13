<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Application;

use Iriven\PhpFormGenerator\Domain\Contract\CsrfManagerInterface;
use Iriven\PhpFormGenerator\Domain\Contract\DataMapperInterface;
use Iriven\PhpFormGenerator\Domain\Contract\FormTypeInterface;
use Iriven\PhpFormGenerator\Domain\Event\EventDispatcher;
use Iriven\PhpFormGenerator\Domain\Form\FormBuilder;
use Iriven\PhpFormGenerator\Domain\Form\FormInterface;
use Iriven\PhpFormGenerator\Infrastructure\Mapping\ArrayDataMapper;
use Iriven\PhpFormGenerator\Infrastructure\Security\NullCsrfManager;

final class FormFactory
{
    public function __construct(
        private readonly ?DataMapperInterface $dataMapper = null,
        private readonly ?CsrfManagerInterface $csrfManager = null,
    ) {
    }

    public function createBuilder(string $name, mixed $data = [], array $options = []): FormBuilder
    {
        $builder = new FormBuilder(
            name: $name,
            dataMapper: $this->dataMapper ?? new ArrayDataMapper(),
            csrfManager: $this->csrfManager ?? new NullCsrfManager(),
            eventDispatcher: new EventDispatcher(),
            formOptions: $options,
        );

        return $builder->setData($data);
    }

    public function create(string $typeClass, mixed $data = [], array $options = []): FormInterface
    {
        /** @var FormTypeInterface $type */
        $type = new $typeClass();
        $resolved = array_replace($type->configureOptions($options), $options);
        $name = (string) ($resolved['name'] ?? strtolower((new \ReflectionClass($typeClass))->getShortName()));
        $builder = $this->createBuilder($name, $data, $resolved);
        $type->buildForm($builder, $resolved);

        return $builder->getForm();
    }
}

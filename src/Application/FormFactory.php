<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Application;

use Iriven\PhpFormGenerator\Domain\Contract\CsrfManagerInterface;
use Iriven\PhpFormGenerator\Domain\Contract\DataMapperInterface;
use Iriven\PhpFormGenerator\Domain\Contract\EventDispatcherInterface;
use Iriven\PhpFormGenerator\Domain\Contract\FormTypeInterface;
use Iriven\PhpFormGenerator\Domain\Form\Form;
use Iriven\PhpFormGenerator\Domain\Form\FormBuilder;
use Iriven\PhpFormGenerator\Infrastructure\Event\SimpleEventDispatcher;
use Iriven\PhpFormGenerator\Infrastructure\Security\NullCsrfManager;

final class FormFactory
{
    public function __construct(
        private readonly ?CsrfManagerInterface $csrfManager = null,
        private readonly ?EventDispatcherInterface $dispatcher = null,
        private readonly ?DataMapperInterface $dataMapper = null,
    ) {
    }

    public function createBuilder(string $name = 'form', array $options = []): FormBuilder
    {
        return new FormBuilder(
            $name,
            $options,
            $this->csrfManager ?? new NullCsrfManager(),
            $this->dispatcher ?? new SimpleEventDispatcher(),
            $this->dataMapper
        );
    }

    public function create(string $typeClass, mixed $data = null, string $name = 'form', array $options = []): Form
    {
        /** @var FormTypeInterface $type */
        $type = new $typeClass();
        $builder = $this->createBuilder($name, $options);
        $type->buildForm($builder, $options);
        if ($data !== null) {
            $builder->setData($data);
        }
        return $builder->getForm();
    }
}

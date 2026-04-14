<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Application;

use Iriven\PhpFormGenerator\Domain\Contract\CaptchaManagerInterface;
use Iriven\PhpFormGenerator\Domain\Contract\CsrfManagerInterface;
use Iriven\PhpFormGenerator\Domain\Contract\EventDispatcherInterface;
use Iriven\PhpFormGenerator\Domain\Contract\FormTypeInterface;
use Iriven\PhpFormGenerator\Domain\Form\Form;
use Iriven\PhpFormGenerator\Domain\Form\FormBuilder;
use Iriven\PhpFormGenerator\Infrastructure\Event\EventDispatcher;
use Iriven\PhpFormGenerator\Infrastructure\Extension\ExtensionRegistry;
use Iriven\PhpFormGenerator\Infrastructure\Options\OptionsResolver;
use Iriven\PhpFormGenerator\Infrastructure\Type\TypeResolver;
use Iriven\PhpFormGenerator\Infrastructure\Security\NullCsrfManager;
use Iriven\PhpFormGenerator\Infrastructure\Security\SessionCaptchaManager;

final class FormFactory
{
    public function __construct(
        private readonly ?CsrfManagerInterface $csrfManager = null,
        private readonly ?EventDispatcherInterface $eventDispatcher = null,
        private readonly ?CaptchaManagerInterface $captchaManager = null,
        private readonly ?ExtensionRegistry $extensionRegistry = null,
    ) {
    }

    /** @param array<string, mixed> $options */
    public function createBuilder(string $name = 'form', mixed $data = null, array $options = []): FormBuilder
    {
        $options['csrf_manager'] = $options['csrf_manager'] ?? $this->csrfManager ?? new NullCsrfManager();
        $options['event_dispatcher'] = $options['event_dispatcher'] ?? $this->eventDispatcher ?? new EventDispatcher();
        $options['captcha_manager'] = $options['captcha_manager'] ?? $this->captchaManager ?? new SessionCaptchaManager();
        $options['csrf_protection'] = $options['csrf_protection'] ?? true;
        $options['extension_registry'] = $options['extension_registry'] ?? $this->extensionRegistry ?? new ExtensionRegistry();

        return new FormBuilder($name, $data, $options);
    }

    /**
     * @param string $typeClass
     * @param array<string, mixed> $options
     */
    public function create(string $typeClass, mixed $data = null, array $options = []): Form
    {
        $builder = $this->createBuilder((string) ($options['name'] ?? 'form'), $data, $options);
        $typeClass = TypeResolver::resolveFormType($typeClass);
        $type = new $typeClass();
        $resolver = new OptionsResolver();
        $type->configureOptions($resolver);
        $resolved = $resolver->resolve($options);
        $type->buildForm($builder, $resolved);

        return $builder->getForm();
    }
}

<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Tests\Fixtures;

use Iriven\PhpFormGenerator\Domain\Constraint\Callback;
use Iriven\PhpFormGenerator\Domain\Constraint\Required;
use Iriven\PhpFormGenerator\Domain\Contract\FormTypeInterface;
use Iriven\PhpFormGenerator\Domain\Contract\OptionsResolverInterface;
use Iriven\PhpFormGenerator\Domain\Event\FormEvents;
use Iriven\PhpFormGenerator\Domain\Event\PreSubmitEvent;
use Iriven\PhpFormGenerator\Domain\Field\CheckboxType;
use Iriven\PhpFormGenerator\Domain\Field\CollectionType;
use Iriven\PhpFormGenerator\Domain\Field\TextType;
use Iriven\PhpFormGenerator\Domain\Form\FormBuilder;

final class ProfileType implements FormTypeInterface
{
    /** @param array<string, mixed> $options */
    public function buildForm(FormBuilder $builder, array $options = []): void
    {
        $this->buildCoreFields($builder);
        $builder
            ->addFormConstraint(new Callback($this->profileNameValidator(...)))
            ->addEventListener(FormEvents::PRE_SUBMIT, $this->trimNameOnPreSubmit(...));
    }

    private function buildCoreFields(FormBuilder $builder): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Name',
                'constraints' => [new Required()],
            ])
            ->add('address', AddressType::class, [
                'label' => 'Primary address',
            ])
            ->add('addresses', CollectionType::class, [
                'label' => 'Addresses',
                'entry_type' => AddressType::class,
                'entry_options' => [],
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
            ])
            ->add('active', CheckboxType::class, [
                'label' => 'Active',
            ]);
    }

    /** @return array<int, string> */
    private function profileNameValidator(mixed $value): array
    {
        if (!is_array($value)) {
            return ['Invalid form data.'];
        }

        return ($value['name'] ?? '') === 'forbidden'
            ? ['This profile name is forbidden.']
            : [];
    }

    private function trimNameOnPreSubmit(PreSubmitEvent $event): void
    {
        $data = $event->getData();
        if (!is_array($data)) {
            return;
        }

        if (isset($data['name']) && is_string($data['name'])) {
            $data['name'] = trim($data['name']);
            $event->setData($data);
        }
    }

    public function configureOptions(OptionsResolverInterface $resolver): void
    {
        $resolver->setDefaults([
            'method' => 'POST',
            'csrf_protection' => false,
        ]);
    }
}

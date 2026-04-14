<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Application\FormType;

use Iriven\PhpFormGenerator\Domain\Constraint\Required;
use Iriven\PhpFormGenerator\Domain\Contract\FormTypeInterface;
use Iriven\PhpFormGenerator\Domain\Contract\OptionsResolverInterface;
use Iriven\PhpFormGenerator\Domain\Field\CollectionType;
use Iriven\PhpFormGenerator\Domain\Field\DatetimeType;
use Iriven\PhpFormGenerator\Domain\Field\SubmitType;
use Iriven\PhpFormGenerator\Domain\Form\FormBuilder;

final class InvoiceType implements FormTypeInterface
{
    /** @param array<string, mixed> $options */
    public function buildForm(FormBuilder $builder, array $options = []): void
    {
        $builder
            ->addFieldset([
                'legend' => 'Invoice',
                'description' => 'Invoice header and customer information.',
            ])
            ->add('customer', CustomerType::class, [
                'label' => 'Customer',
            ])
            ->add('issuedAt', DatetimeType::class, [
                'label' => 'Issued at',
                'constraints' => [new Required()],
            ])
            ->add('items', CollectionType::class, [
                'label' => 'Line items',
                'entry_type' => InvoiceLineType::class,
                'entry_options' => [],
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
            ])
            ->endFieldset()
            ->add('submit', SubmitType::class, [
                'label' => 'Save invoice',
            ]);
    }

    public function configureOptions(OptionsResolverInterface $resolver): void
    {
        $resolver->setDefaults([
            'method' => 'POST',
        ]);
    }
}

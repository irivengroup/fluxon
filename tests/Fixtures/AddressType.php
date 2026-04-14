<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Tests\Fixtures;

use Iriven\PhpFormGenerator\Domain\Contract\FormTypeInterface;
use Iriven\PhpFormGenerator\Domain\Field\TextType;
use Iriven\PhpFormGenerator\Domain\Form\FormBuilder;

final class AddressType implements FormTypeInterface
{
    public function buildForm(FormBuilder $builder, array $options = []): void
    {
        $builder
            ->add('street', TextType::class, ['label' => 'Street'])
            ->add('city', TextType::class, ['label' => 'City']);
    }

    public function configureOptions(array $options = []): array
    {
        return $options;
    }
}

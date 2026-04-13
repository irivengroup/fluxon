<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Application\Example;

use Iriven\PhpFormGenerator\Domain\Constraint\Email;
use Iriven\PhpFormGenerator\Domain\Constraint\Length;
use Iriven\PhpFormGenerator\Domain\Constraint\Required;
use Iriven\PhpFormGenerator\Domain\Contract\FormBuilderInterface;
use Iriven\PhpFormGenerator\Domain\Contract\FormTypeInterface;
use Iriven\PhpFormGenerator\Domain\Field\CountryType;
use Iriven\PhpFormGenerator\Domain\Field\EmailType;
use Iriven\PhpFormGenerator\Domain\Field\SubmitType;
use Iriven\PhpFormGenerator\Domain\Field\TextType;
use Iriven\PhpFormGenerator\Domain\Field\TextareaType;

final class ContactType implements FormTypeInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options = []): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'constraints' => [new Required(), new Length(min: 2, max: 120)],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'constraints' => [new Required(), new Email()],
            ])
            ->add('country', CountryType::class, [
                'label' => 'Pays',
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Message',
                'constraints' => [new Required(), new Length(min: 10)],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Envoyer',
            ]);
    }

    public function configureOptions(array $options = []): array
    {
        return [
            'name' => 'contact',
            'method' => 'POST',
            'csrf_protection' => true,
        ];
    }
}

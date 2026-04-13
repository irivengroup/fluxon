<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Application\Type;

use Iriven\PhpFormGenerator\Domain\Constraint\Email;
use Iriven\PhpFormGenerator\Domain\Constraint\Length;
use Iriven\PhpFormGenerator\Domain\Constraint\Required;
use Iriven\PhpFormGenerator\Domain\Contract\FormTypeInterface;
use Iriven\PhpFormGenerator\Domain\Field\CountryType;
use Iriven\PhpFormGenerator\Domain\Field\EmailType;
use Iriven\PhpFormGenerator\Domain\Field\SubmitType;
use Iriven\PhpFormGenerator\Domain\Field\TextType;
use Iriven\PhpFormGenerator\Domain\Field\TextareaType;
use Iriven\PhpFormGenerator\Domain\Form\FormBuilder;

final class ContactType implements FormTypeInterface
{
    public function buildForm(FormBuilder $builder, array $options = []): void
    {
        $builder
            ->addFieldset([
                'legend' => 'Identity',
                'description' => 'Primary contact information',
            ])
            ->add('name', TextType::class, [
                'label' => 'Name',
                'constraints' => [new Required(), new Length(min: 2, max: 120)],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'constraints' => [new Required(), new Email()],
            ])
            ->add('country', CountryType::class, [
                'label' => 'Country',
                'placeholder' => 'Choose a country',
            ])
            ->endFieldset()
            ->add('message', TextareaType::class, [
                'label' => 'Message',
                'constraints' => [new Required(), new Length(min: 10)],
                'help' => 'Tell us what you need.',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Send',
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }
}

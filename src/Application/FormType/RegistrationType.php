<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Application\FormType;

use Iriven\PhpFormGenerator\Domain\Constraint\Callback;
use Iriven\PhpFormGenerator\Domain\Constraint\Email;
use Iriven\PhpFormGenerator\Domain\Constraint\Length;
use Iriven\PhpFormGenerator\Domain\Constraint\Required;
use Iriven\PhpFormGenerator\Domain\Contract\FormTypeInterface;
use Iriven\PhpFormGenerator\Domain\Contract\OptionsResolverInterface;
use Iriven\PhpFormGenerator\Domain\Field\CaptchaType;
use Iriven\PhpFormGenerator\Domain\Field\CheckboxType;
use Iriven\PhpFormGenerator\Domain\Field\EmailType;
use Iriven\PhpFormGenerator\Domain\Field\PasswordType;
use Iriven\PhpFormGenerator\Domain\Field\SubmitType;
use Iriven\PhpFormGenerator\Domain\Form\FormBuilder;

final class RegistrationType implements FormTypeInterface
{
    /** @param array<string, mixed> $options */
    public function buildForm(FormBuilder $builder, array $options = []): void
    {
        $builder
            ->addFieldset([
                'legend' => 'Account registration',
                'description' => 'Create a new user account with a validated captcha.',
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email address',
                'constraints' => [new Required(), new Email()],
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Password',
                'constraints' => [new Required(), new Length(8, 255)],
            ])
            ->add('confirmPassword', PasswordType::class, [
                'label' => 'Confirm password',
                'constraints' => [new Required()],
            ])
            ->add('acceptTerms', CheckboxType::class, [
                'label' => 'I accept the terms',
                'constraints' => [
                    new Callback(static function (mixed $value): array {
                        return $value === true ? [] : ['You must accept the terms to register.'];
                    }),
                ],
            ])
            ->add('captcha', CaptchaType::class, [
                'label' => 'Security code',
                'min_length' => 5,
                'max_length' => 8,
                'constraints' => [new Required()],
            ])
            ->endFieldset()
            ->add('submit', SubmitType::class, [
                'label' => 'Create account',
            ]);

        $builder->addFormConstraint(new Callback(static function (mixed $value): array {
            if (!is_array($value)) {
                return ['Invalid registration payload.'];
            }

            return ($value['password'] ?? null) === ($value['confirmPassword'] ?? null)
                ? []
                : ['Password confirmation does not match.'];
        }));
    }

    public function configureOptions(OptionsResolverInterface $resolver): void
    {
        $resolver->setDefaults([
            'method' => 'POST',
            'captcha_enabled' => true,
        ]);
    }
}

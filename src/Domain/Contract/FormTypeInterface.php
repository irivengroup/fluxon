<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Contract;

use Iriven\PhpFormGenerator\Domain\Form\FormBuilder;

interface FormTypeInterface
{
    public function buildForm(FormBuilder $builder, array $options = []): void;
}

<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Field;

final class YesNoType extends ChoiceType
{
    public function normalizeOptions(array $options): array
    {
        $options = parent::normalizeOptions($options);
        $options['choices'] = $options['choices'] ?? ['Yes' => '1', 'No' => '0'];
        return $options;
    }
}

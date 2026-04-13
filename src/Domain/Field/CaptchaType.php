<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Field;

final class CaptchaType extends AbstractFieldType
{
    public function renderType(): string
    {
        return 'text';
    }

    public function normalizeOptions(array $options): array
    {
        $options = parent::normalizeOptions($options);
        $operators = ['+', 'x', '-'];
        $operator = $options['captcha_operator'] ?? $operators[random_int(0, 2)];

        if (!isset($options['captcha_answer'], $options['captcha_question'])) {
            switch ($operator) {
                case 'x':
                    do {
                        $first = random_int(3, 7);
                        $second = random_int(2, 6);
                        $response = $first * $second;
                    } while ($response > 35);
                    break;
                case '-':
                    do {
                        $first = random_int(6, 11);
                        $second = random_int(1, 8);
                        $response = $first - $second;
                    } while ($response < 2);
                    break;
                default:
                    $first = random_int(3, 8);
                    $second = random_int(1, 7);
                    $response = $first + $second;
                    $operator = '+';
            }
            $options['captcha_question'] = sprintf('%d %s %d', $first, $operator, $second);
            $options['captcha_answer'] = (string) $response;
        }

        $label = (string) ($options['label'] ?? 'Security Code');
        $options['label'] = $label . '. ' . $options['captcha_question'];
        $options['attr']['maxlength'] ??= 2;
        $options['attr']['pattern'] ??= '[0-9]{1,2}';
        $options['attr']['placeholder'] ??= 'Enter result';
        $options['required'] ??= true;
        return $options;
    }
}

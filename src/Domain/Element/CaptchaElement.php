<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Element;

final class CaptchaElement extends InputElement
{
    public function __construct(string $label = 'Security Code', array $attributes = [])
    {
        [$question, $response] = $this->generateChallenge();
        parent::__construct($label . '. ' . $question, 'text', $attributes);

        $originalName = $label;
        $this->attributes
            ->set('name', $originalName)
            ->createElementId($originalName)
            ->set('maxlength', 2)
            ->set('required', 'required')
            ->set('pattern', '[0-9]{1,2}')
            ->set('placeholder', 'Enter result')
            ->set('capresponse', $response);
    }

    private function generateChallenge(): array
    {
        $operators = ['+', 'x', '-'];
        $operator = $operators[random_int(0, 2)];

        switch ($operator) {
            case 'x':
                do {
                    $a = random_int(3, 7);
                    $b = random_int(2, 6);
                    $response = $a * $b;
                } while ($response > 35);
                break;
            case '-':
                do {
                    $a = random_int(6, 11);
                    $b = random_int(1, 8);
                    $response = $a - $b;
                } while ($response < 2);
                break;
            default:
                $a = random_int(3, 8);
                $b = random_int(1, 7);
                $response = $a + $b;
                break;
        }

        return [sprintf('%d %s %d', $a, $operator, $b), $response];
    }
}

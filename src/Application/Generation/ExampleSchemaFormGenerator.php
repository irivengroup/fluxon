<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Application\Generation;

/** @api */
final class ExampleSchemaFormGenerator
{
    public function __construct(
        private readonly DtoFormGuesser $guesser = new DtoFormGuesser(),
    ) {
    }

    /**
     * @param object|array<string, mixed> $sample
     * @return array{fields: array<string, array{type: string, required?: bool, label?: string}>}
     */
    public function generate(object|array $sample): array
    {
        $fields = $this->guesser->guess($sample);

        return ['fields' => $fields];
    }
}

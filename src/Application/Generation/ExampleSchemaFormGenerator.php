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
     * @param array<string, mixed> $sample
     * @return array<string, array<string, string>>
     */
    public function generate(array $sample): array
    {
        $fields = [];
        foreach ($this->guesser->guess($sample) as $name => $type) {
            $fields[$name] = ['type' => $type];
        }

        return ['fields' => $fields];
    }
}

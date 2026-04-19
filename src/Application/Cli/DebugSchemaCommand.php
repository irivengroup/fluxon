<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Application\Cli;

use Iriven\PhpFormGenerator\Application\FormSchemaManager;
use Iriven\PhpFormGenerator\Domain\Form\Form;

/**
 * @api
 */
final class DebugSchemaCommand implements CliCommandInterface
{
    public function __construct(
        private readonly FormSchemaManager $schemaManager,
        private readonly Form $form,
    ) {
    }

    public function name(): string
    {
        return 'debug:schema';
    }

    /**
     * @param array<int, string> $args
     */
    public function run(array $args = []): string
    {
        return json_encode($this->schemaManager->exportHeadless($this->form), JSON_PRETTY_PRINT) ?: '{}';
    }
}

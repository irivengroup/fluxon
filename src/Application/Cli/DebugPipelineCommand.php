<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Application\Cli;

use Iriven\PhpFormGenerator\Application\Debug\RuntimeInspector;
use Iriven\PhpFormGenerator\Application\FormFactory;

/** @api */
final class DebugPipelineCommand implements CliCommandInterface
{
    public function name(): string
    {
        return 'debug:pipeline';
    }

    /**
     * @param array<int, string> $args
     */
    public function run(array $args = []): string
    {
        $name = $args[0] ?? 'contact';
        $form = (new FormFactory())->createBuilder($name)->getForm();
        $data = (new RuntimeInspector())->inspect($form, null, ['trim', 'csrf'], true, 'debug-pipeline');

        return json_encode($data, JSON_PRETTY_PRINT) ?: '{}';
    }
}

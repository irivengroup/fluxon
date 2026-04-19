<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Application\Cli;

/**
 * @api
 */
final class MakePluginCommand implements CliCommandInterface
{
    public function name(): string
    {
        return 'make:plugin';
    }

    /**
     * @param array<int, string> $args
     */
    public function run(array $args = []): string
    {
        $name = $args[0] ?? 'GeneratedPlugin';

        return <<<TXT
<?php

declare(strict_types=1);

use Iriven\PhpFormGenerator\Domain\Contract\PluginInterface;
use Iriven\PhpFormGenerator\Domain\Contract\FieldTypeRegistryInterface;
use Iriven\PhpFormGenerator\Domain\Contract\FormTypeRegistryInterface;
use Iriven\PhpFormGenerator\Infrastructure\Extension\ExtensionRegistry;

final class {$name} implements PluginInterface
{
    public function registerFieldTypes(FieldTypeRegistryInterface \$registry): void
    {
    }

    public function registerFormTypes(FormTypeRegistryInterface \$registry): void
    {
    }

    public function registerExtensions(ExtensionRegistry \$registry): void
    {
    }
}
TXT;
    }
}

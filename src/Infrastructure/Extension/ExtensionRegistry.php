<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Infrastructure\Extension;

use Iriven\PhpFormGenerator\Domain\Contract\ExtensionInterface;
use Throwable;

/**
 * @api
 */
final class ExtensionRegistry
{
    /** @var list<ExtensionInterface> */
    private array $extensions = [];

    public function addFieldExtension(ExtensionInterface $extension): void
    {
        $this->extensions[] = $extension;
    }

    /** @return list<ExtensionInterface> */
    public function all(): array
    {
        return $this->extensions;
    }

    /** @return list<ExtensionInterface> */
    public function for(string $type): array
    {
        return array_values(array_filter(
            $this->extensions,
            static function (ExtensionInterface $extension) use ($type): bool {
                try {
                    return $extension->supports($type);
                } catch (Throwable) {
                    return false;
                }
            }
        ));
    }

    /**
     * @param array<string, mixed> $options
     * @return array<string, mixed>
     */
    public function apply(string $type, array $options): array
    {
        foreach ($this->for($type) as $extension) {
            try {
                $next = $extension->apply($options);
                if (is_array($next)) {
                    $options = $next;
                }
            } catch (Throwable) {
            }
        }

        return $options;
    }
}

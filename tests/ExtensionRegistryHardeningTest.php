<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Tests;

use Iriven\PhpFormGenerator\Domain\Contract\ExtensionInterface;
use Iriven\PhpFormGenerator\Infrastructure\Extension\ExtensionRegistry;
use PHPUnit\Framework\TestCase;

final class ExtensionRegistryHardeningTest extends TestCase
{
    public function testFaultySupportsDoesNotBreakResolution(): void
    {
        $registry = new ExtensionRegistry();
        $registry->addFieldExtension(new class implements ExtensionInterface {
            public function supports(string $type): bool { throw new \RuntimeException('supports failed'); }
            public function apply(array $options): array { return $options; }
        });

        self::assertSame([], $registry->for('text'));
    }

    public function testFaultyApplyDoesNotBreakRuntime(): void
    {
        $registry = new ExtensionRegistry();
        $registry->addFieldExtension(new class implements ExtensionInterface {
            public function supports(string $type): bool { return $type === 'text'; }
            public function apply(array $options): array { throw new \RuntimeException('apply failed'); }
        });

        self::assertSame(['a' => true], $registry->apply('text', ['a' => true]));
    }

    public function testExtensionsAreAppliedInRegistrationOrder(): void
    {
        $registry = new ExtensionRegistry();
        $registry->addFieldExtension(new class implements ExtensionInterface {
            public function supports(string $type): bool { return $type === 'text'; }
            public function apply(array $options): array { $options['steps'][] = 'first'; return $options; }
        });
        $registry->addFieldExtension(new class implements ExtensionInterface {
            public function supports(string $type): bool { return $type === 'text'; }
            public function apply(array $options): array { $options['steps'][] = 'second'; return $options; }
        });

        $result = $registry->apply('text', ['steps' => []]);
        self::assertSame(['first', 'second'], $result['steps']);
    }
}

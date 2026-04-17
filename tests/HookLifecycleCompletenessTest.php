<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Tests;

use Iriven\PhpFormGenerator\Application\FormRuntimePipeline;
use PHPUnit\Framework\TestCase;

final class HookLifecycleCompletenessTest extends TestCase
{
    public function testOfficialLifecycleHooksRemainComplete(): void
    {
        self::assertSame(
            ['before_build','after_build','before_submit','after_submit','before_render','after_render','before_export','after_export'],
            (new FormRuntimePipeline())->stages()
        );
    }
}

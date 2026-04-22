<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Application\Debug;

use Iriven\PhpFormGenerator\Application\FormRuntimeContext;
use Iriven\PhpFormGenerator\Application\Observability\InMemoryMetricsCollector;
use Iriven\PhpFormGenerator\Domain\Form\Form;

/** @api */
final class RuntimeInspector
{
    public function __construct(
        private readonly InMemoryMetricsCollector $metrics = new InMemoryMetricsCollector(),
    ) {
    }

    /**
     * @param array<int, string> $extensions
     * @return array<string, mixed>
     */
    public function inspect(Form $form, ?FormRuntimeContext $runtimeContext = null, array $extensions = [], bool $cacheHit = false, ?string $cacheKey = null): array
    {
        $payload = $runtimeContext?->payload();
        $channel = $payload !== null ? (string) $payload->metadataValue('channel', 'headless') : 'headless';
        $fields = $form->fields();

        $this->metrics->record('fields_count', count($fields));
        $this->metrics->record('extensions_count', count($extensions));
        $this->metrics->record('cache_hit', $cacheHit);

        return [
            'form' => $form->getName(),
            'fields_count' => count($fields),
            'extensions_applied' => array_values($extensions),
            'theme' => $runtimeContext?->theme() ?? 'default',
            'channel' => $channel !== '' ? $channel : 'headless',
            'cache' => ['hit' => $cacheHit, 'key' => $cacheKey],
            'timings' => ['build' => 0.0, 'render' => 0.0],
            'metrics' => $this->metrics->all(),
        ];
    }
}

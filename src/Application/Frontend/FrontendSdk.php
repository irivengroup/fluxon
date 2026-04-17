<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Application\Frontend;

use Iriven\PhpFormGenerator\Application\FormRuntimeContext;
use Iriven\PhpFormGenerator\Application\FormSchemaManager;
use Iriven\PhpFormGenerator\Domain\Form\Form;

/**
 * @api
 */
final class FrontendSdk
{
    public function __construct(
        private readonly FormSchemaManager $schemaManager,
        private readonly FrontendSdkConfig $config = new FrontendSdkConfig(),
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function buildSchema(Form $form, ?FormRuntimeContext $runtimeContext = null): array
    {
        $schema = $this->schemaManager->exportHeadless($form, $runtimeContext);
        $schema += ['form' => [], 'fields' => [], 'ui' => [], 'runtime' => [], 'validation' => []];

        $schema['sdk'] = [
            'framework' => $this->config->framework(),
            'schema_version' => $this->config->schemaVersion(),
            'defaults' => $this->config->defaults(),
        ];

        return $schema;
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function buildSubmissionPayload(Form $form, array $data): array
    {
        return [
            'form' => $form->getName(),
            'payload' => $data,
            'sdk' => [
                'framework' => $this->config->framework(),
                'schema_version' => $this->config->schemaVersion(),
            ],
        ];
    }

    public function getSchemaVersion(): string
    {
        return $this->config->schemaVersion();
    }

    public function getFramework(): string
    {
        return $this->config->framework();
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function validatePayload(array $data): array
    {
        return $data;
    }
}

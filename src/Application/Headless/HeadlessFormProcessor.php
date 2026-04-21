<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Application\Headless;

use Iriven\PhpFormGenerator\Application\FormSchemaManager;
use Iriven\PhpFormGenerator\Domain\Form\Form;
use Iriven\PhpFormGenerator\Infrastructure\Schema\ArraySchemaExporter;

/**
 * @api
 */
final class HeadlessFormProcessor
{
    public function __construct(
        private readonly ?FormSchemaManager $schemaManager = null,
        private readonly HeadlessResponseBuilder $responseBuilder = new HeadlessResponseBuilder(),
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function schema(Form $form): array
    {
        $manager = $this->schemaManager ?? new FormSchemaManager(new ArraySchemaExporter());

        return $manager->exportHeadless($form);
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function validate(Form $form, array $payload): array
    {
        return $this->responseBuilder->build(
            new HeadlessFormState(true, true, $payload, [], [
                'mode' => 'validate',
                'form' => $form->getName(),
            ])
        );
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function submit(Form $form, array $payload): array
    {
        return $this->responseBuilder->build(
            new HeadlessFormState(true, true, $payload, [], [
                'mode' => 'submit',
                'form' => $form->getName(),
            ])
        );
    }

    /**
     * @param array<string, mixed> $payload
     * @param array<string, mixed> $errors
     * @return array<string, mixed>
     */
    public function invalid(Form $form, array $payload, array $errors): array
    {
        return $this->responseBuilder->build(
            new HeadlessFormState(true, false, $payload, $errors, [
                'mode' => 'submit',
                'form' => $form->getName(),
            ])
        );
    }
}

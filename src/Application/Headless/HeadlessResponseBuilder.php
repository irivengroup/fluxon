<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Application\Headless;

/**
 * @api
 */
final class HeadlessResponseBuilder
{
    public function __construct(
        private readonly HeadlessPayloadNormalizer $payloadNormalizer = new HeadlessPayloadNormalizer(),
        private readonly HeadlessErrorNormalizer $errorNormalizer = new HeadlessErrorNormalizer(),
    ) {
    }

    /**
     * @return array{
     *   state: array{submitted: bool, valid: bool},
     *   payload: array<string, mixed>,
     *   errors: array<string, mixed>,
     *   metadata: array<string, mixed>
     * }
     */
    public function build(HeadlessFormState $state): array
    {
        $payload = $this->payloadNormalizer->normalize($state->payload());
        $errors = $this->errorNormalizer->normalize($state->errors());
        $metadata = $state->metadata();

        return [
            'state' => [
                'submitted' => $state->submitted(),
                'valid' => $state->valid(),
            ],
            'payload' => $payload,
            'errors' => $errors,
            'metadata' => $metadata,
        ];
    }
}

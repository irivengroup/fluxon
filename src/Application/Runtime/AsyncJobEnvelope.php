<?php
declare(strict_types=1);

namespace Iriven\Fluxon\Application\Runtime;

/** @api */
final class AsyncJobEnvelope
{
    /**
     * @param array<string, mixed> $payload
     */
    public function __construct(
        private readonly string $jobId,
        private readonly string $action,
        private readonly string $formName,
        private readonly array $payload = [],
        private readonly ?ExecutionContext $context = null,
    ) {
    }

    public function jobId(): string
    {
        return $this->jobId;
    }

    public function action(): string
    {
        return $this->action;
    }

    public function formName(): string
    {
        return $this->formName;
    }

    /**
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        return $this->payload;
    }

    public function context(): ?ExecutionContext
    {
        return $this->context;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'job_id' => $this->jobId,
            'action' => $this->action,
            'form_name' => $this->formName,
            'payload' => $this->payload,
            'context' => $this->context === null ? null : [
                'request_id' => $this->context->requestId(),
                'timestamp' => $this->context->timestamp(),
                'source' => $this->context->source(),
            ],
        ];
    }

    public function isValid(): bool
    {
        return $this->jobId !== '' && $this->action !== '' && $this->formName !== '';
    }
}

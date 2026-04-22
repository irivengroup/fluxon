<?php
declare(strict_types=1);

namespace Iriven\Fluxon\Application\Runtime;

/** @api */
final class AsyncRuntimeDispatcher
{
    public function __construct(
        private readonly QueueTransport $transport = new QueueTransport(),
        private readonly JobSerializer $serializer = new JobSerializer(),
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function dispatch(string $action, string $formName, array $payload = [], ?ExecutionContext $context = null): array
    {
        $job = new AsyncJobEnvelope(
            uniqid('job_', true),
            $action,
            $formName,
            $payload,
            $context,
        );

        if (!$job->isValid()) {
            return [
                'transport' => 'queue',
                'status' => 'invalid',
                'payload' => [],
                'queue_size' => $this->transport->size(),
            ];
        }

        return $this->transport->send([
            'serialized_job' => $this->serializer->serialize($job),
            'job' => $job->toArray(),
        ]);
    }

    public function transport(): QueueTransport
    {
        return $this->transport;
    }
}

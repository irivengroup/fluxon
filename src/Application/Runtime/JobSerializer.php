<?php
declare(strict_types=1);

namespace Iriven\Fluxon\Application\Runtime;

use JsonException;

/** @api */
final class JobSerializer
{
    public function serialize(AsyncJobEnvelope $job): string
    {
        return json_encode($job->toArray(), JSON_THROW_ON_ERROR);
    }

    public function deserialize(string $payload): AsyncJobEnvelope
    {
        try {
            /** @var array<string, mixed> $data */
            $data = json_decode($payload, true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return new AsyncJobEnvelope('invalid', 'submit', 'unknown', [], null);
        }

        $context = null;
        if (is_array($data['context'] ?? null)) {
            $context = new ExecutionContext(
                (string) ($data['context']['request_id'] ?? 'unknown'),
                (string) ($data['context']['timestamp'] ?? ''),
                (string) ($data['context']['source'] ?? 'async'),
            );
        }

        return new AsyncJobEnvelope(
            (string) ($data['job_id'] ?? 'unknown'),
            (string) ($data['action'] ?? 'submit'),
            (string) ($data['form_name'] ?? 'unknown'),
            is_array($data['payload'] ?? null) ? $data['payload'] : [],
            $context,
        );
    }
}

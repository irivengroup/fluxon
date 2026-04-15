<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Infrastructure\Http;

use Iriven\PhpFormGenerator\Domain\Contract\RequestInterface;
use Iriven\PhpFormGenerator\Domain\ValueObject\UploadedFile;

final class NativeRequest implements RequestInterface
{
    /**
     * @param array<string, mixed> $request
     * @param array<string, mixed> $files
     */
    public function __construct(
        private readonly string $method = 'GET',
        private readonly array $request = [],
        private readonly array $files = [],
    ) {
    }

    public function getMethod(): string
    {
        return strtoupper($this->method);
    }

    /** @return array<string, mixed> */
    public function all(): array
    {
        return array_replace_recursive($this->request, $this->normalizeFiles($this->files));
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $data = $this->all();

        return $data[$key] ?? $default;
    }

    /**
     * @param array<string, mixed> $files
     * @return array<string, mixed>
     */
    private function normalizeFiles(array $files): array
    {
        $normalized = [];

        foreach ($files as $field => $spec) {
            $normalized[$field] = $this->normalizeFileSpec($spec);
        }

        return $normalized;
    }

    private function normalizeFileSpec(mixed $spec): mixed
    {
        if (!$this->isUploadedFileSpec($spec)) {
            return $spec;
        }

        if (is_array($spec['name'])) {
            return $this->normalizeMultipleUploadedFiles($spec);
        }

        return $this->uploadedFileFromScalarSpec($spec);
    }

    private function isUploadedFileSpec(mixed $spec): bool
    {
        return is_array($spec)
            && isset($spec['name'], $spec['type'], $spec['tmp_name'], $spec['error'], $spec['size']);
    }

    /**
     * @param array{name:mixed,type:mixed,tmp_name:mixed,error:mixed,size:mixed} $spec
     * @return array<int, UploadedFile>
     */
    private function normalizeMultipleUploadedFiles(array $spec): array
    {
        $items = [];
        foreach (array_keys($spec['name']) as $index) {
            $items[] = new UploadedFile(
                (string) $spec['name'][$index],
                (string) $spec['type'][$index],
                (int) $spec['size'][$index],
                (string) $spec['tmp_name'][$index],
                (int) $spec['error'][$index],
            );
        }

        return $items;
    }

    /**
     * @param array{name:mixed,type:mixed,tmp_name:mixed,error:mixed,size:mixed} $spec
     */
    private function uploadedFileFromScalarSpec(array $spec): UploadedFile
    {
        return new UploadedFile(
            (string) $spec['name'],
            (string) $spec['type'],
            (int) $spec['size'],
            (string) $spec['tmp_name'],
            (int) $spec['error'],
        );
    }
}

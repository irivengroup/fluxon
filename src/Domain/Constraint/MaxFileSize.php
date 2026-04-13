<?php
declare(strict_types=1);
namespace Iriven\PhpFormGenerator\Domain\Constraint;
use Iriven\PhpFormGenerator\Domain\Contract\ConstraintInterface;
use Iriven\PhpFormGenerator\Domain\Validation\ValidationError;
use Iriven\PhpFormGenerator\Domain\ValueObject\UploadedFile;
final class MaxFileSize implements ConstraintInterface
{
    public function __construct(private readonly int $maxBytes, private readonly string $message = 'File is too large.') {}
    public function validate(mixed $value, array $context = []): array
    {
        if (!$value instanceof UploadedFile) { return []; }
        return $value->size <= $this->maxBytes ? [] : [new ValidationError($this->message, $context['field'] ?? null)];
    }
}

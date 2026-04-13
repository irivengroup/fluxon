<?php
declare(strict_types=1);
namespace Iriven\PhpFormGenerator\Domain\Constraint;
use Iriven\PhpFormGenerator\Domain\Contract\ConstraintInterface;
use Iriven\PhpFormGenerator\Domain\Validation\ValidationError;
use Iriven\PhpFormGenerator\Domain\ValueObject\UploadedFile;
final class File implements ConstraintInterface
{
    public function __construct(private readonly string $message = 'Invalid file upload.') {}
    public function validate(mixed $value, array $context = []): array
    {
        if ($value === null || $value === '') { return []; }
        if ($value instanceof UploadedFile) { return []; }
        if (is_array($value) && array_is_list($value)) {
            foreach ($value as $file) {
                if (!$file instanceof UploadedFile) { return [new ValidationError($this->message, $context['field'] ?? null)]; }
            }
            return [];
        }
        return [new ValidationError($this->message, $context['field'] ?? null)];
    }
}

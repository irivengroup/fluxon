<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Form;

use Iriven\PhpFormGenerator\Domain\Contract\RequestInterface;

interface FormInterface
{
    public function handleRequest(RequestInterface $request): void;
    public function isSubmitted(): bool;
    public function isValid(): bool;
    public function getData(): mixed;
    public function setData(mixed $data): void;
    public function createView(): FormView;
    public function getErrors(bool $deep = true): array;
    public function get(string $name): Field;
    public function getName(): string;
    public function getCsrfToken(): ?string;
}

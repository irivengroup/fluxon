<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Application;

use Iriven\PhpFormGenerator\Application\Runtime\RuntimePayload;
use Iriven\PhpFormGenerator\Domain\Form\Form;

/**
 * @api
 */
final class FormRuntimeContext
{
    private RuntimePayload $payload;

    /**
     * @param array<string, mixed> $metadata
     */
    public function __construct(
        private readonly Form $form,
        private readonly ?string $theme = null,
        private readonly ?string $renderer = null,
        private readonly array $metadata = [],
    ) {
        $this->payload = new RuntimePayload($theme, $renderer, $metadata);
    }

    public function form(): Form
    {
        return $this->form;
    }

    public function theme(): ?string
    {
        return $this->theme;
    }

    public function renderer(): ?string
    {
        return $this->renderer;
    }

    /**
     * @return array<string, mixed>
     */
    public function metadata(): array
    {
        return $this->metadata;
    }

    public function payload(): RuntimePayload
    {
        return $this->payload;
    }
}

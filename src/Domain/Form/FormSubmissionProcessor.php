<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Form;

use Iriven\PhpFormGenerator\Domain\Contract\RequestInterface;
use Iriven\PhpFormGenerator\Domain\Event\PostSubmitEvent;
use Iriven\PhpFormGenerator\Domain\Event\PreSubmitEvent;
use Iriven\PhpFormGenerator\Domain\Event\SubmitEvent;
use Iriven\PhpFormGenerator\Infrastructure\Translation\TranslatorInterface;

final class FormSubmissionProcessor
{
    private FormFieldSubmissionProcessor $fieldSubmissionProcessor;

    public function __construct(
        private readonly FormValidationProcessor $validationProcessor = new FormValidationProcessor(),
        private readonly FormDataMappingProcessor $mappingProcessor = new FormDataMappingProcessor(),
    ) {
        $this->fieldSubmissionProcessor = new FormFieldSubmissionProcessor($this->validationProcessor);
    }

    public function handleRequest(Form $form, RequestInterface $request): void
    {
        if (!$this->requestMatchesFormMethod($form, $request)) {
            return;
        }

        $payload = $this->extractPayload($form, $request);
        if ($payload === null) {
            return;
        }

        $form->setSubmitted(true);
        $payload = $this->dispatchPreSubmit($form, $payload);
        $this->validateCsrf($form, $payload);
        $this->submitAllFields($form, $payload);
        $this->validationProcessor->validateFormConstraints($form);
        $this->dispatchSubmit($form, $payload);
        $this->mapIfValid($form);
        $this->dispatchPostSubmit($form);
    }

    private function requestMatchesFormMethod(Form $form, RequestInterface $request): bool
    {
        return strtoupper((string) ($form->options()['method'] ?? 'POST')) === $request->getMethod();
    }

    /**
     * @return array<string, mixed>|null
     */
    private function extractPayload(Form $form, RequestInterface $request): ?array
    {
        $payload = $request->get($form->getName(), []);

        return is_array($payload) ? $payload : null;
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    private function dispatchPreSubmit(Form $form, array $payload): array
    {
        $preSubmit = new PreSubmitEvent($form, $payload);
        $form->dispatch('form.pre_submit', $preSubmit);

        return is_array($preSubmit->getData()) ? $preSubmit->getData() : $payload;
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function validateCsrf(Form $form, array $payload): void
    {
        if (($form->options()['csrf_protection'] ?? false) !== true) {
            return;
        }

        $tokenField = (string) ($form->options()['csrf_field_name'] ?? '_token');
        $tokenId = (string) ($form->options()['csrf_token_id'] ?? $form->getName());
        $csrfManager = $form->options()['csrf_manager'] ?? null;

        if ($csrfManager !== null && !$csrfManager->isTokenValid($tokenId, is_string($payload[$tokenField] ?? null) ? $payload[$tokenField] : null)) {
            $form->appendError('_form', $this->trans($form, 'csrf.invalid', 'Invalid CSRF token.'));
            $form->setValid(false);
        }
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function submitAllFields(Form $form, array $payload): void
    {
        foreach ($form->fields() as $name => $field) {
            $raw = $payload[$name] ?? null;
            $form->setSubmittedValue(
                $name,
                $this->fieldSubmissionProcessor->submitField($form, $field, $raw, $name)
            );
        }
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function dispatchSubmit(Form $form, array $payload): void
    {
        $form->dispatch('form.submit', new SubmitEvent($form, $form->submittedValues(), ['payload' => $payload]));
    }

    private function mapIfValid(Form $form): void
    {
        if ($form->isCurrentlyValid()) {
            $this->mappingProcessor->map($form);
        }
    }

    private function dispatchPostSubmit(Form $form): void
    {
        $form->dispatch('form.post_submit', new PostSubmitEvent($form, $form->rawData(), ['valid' => $form->isCurrentlyValid()]));
    }

    /**
     * @param array<string, scalar|null> $parameters
     */
    private function trans(Form $form, string $key, string $fallback, array $parameters = []): string
    {
        $translator = $form->options()['translator'] ?? null;

        if ($translator instanceof TranslatorInterface) {
            return $translator->trans($key, $parameters);
        }

        $message = $fallback;
        foreach ($parameters as $name => $value) {
            $message = str_replace('{{' . $name . '}}', (string) $value, $message);
        }

        return $message;
    }
}

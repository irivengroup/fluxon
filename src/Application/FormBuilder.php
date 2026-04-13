<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Application;

use Iriven\PhpFormGenerator\Domain\Element\CaptchaElement;
use Iriven\PhpFormGenerator\Domain\Element\ChoiceElement;
use Iriven\PhpFormGenerator\Domain\Element\DatalistElement;
use Iriven\PhpFormGenerator\Domain\Element\ElementInterface;
use Iriven\PhpFormGenerator\Domain\Element\FileElement;
use Iriven\PhpFormGenerator\Domain\Element\InputElement;
use Iriven\PhpFormGenerator\Domain\Element\SelectElement;
use Iriven\PhpFormGenerator\Domain\Element\TextareaElement;
use Iriven\PhpFormGenerator\Domain\Form\Fieldset;
use Iriven\PhpFormGenerator\Domain\Form\Form;
use Iriven\PhpFormGenerator\Infrastructure\Request\RequestDataProviderInterface;

final class FormBuilder
{
    private ?Form $form = null;
    private ?Fieldset $fieldset = null;

    public function __construct(private readonly RequestDataProviderInterface $request)
    {
    }

    public function open(array $attributes = []): self
    {
        $this->form = new Form($attributes);
        $this->fieldset = null;

        return $this;
    }

    public function close(): string
    {
        $this->assertFormOpen();

        if ($this->fieldset !== null) {
            $this->form?->append($this->fieldset->renderClose());
            $this->fieldset = null;
        }

        $html = $this->form?->render() ?? '';
        $this->form = null;

        return $html;
    }

    public function addFieldset(array $attributes = []): self
    {
        $this->assertFormOpen();

        if ($this->fieldset !== null) {
            $this->form?->append($this->fieldset->renderClose());
        }

        $this->fieldset = new Fieldset($attributes);
        $this->form?->append($this->fieldset->renderOpen());

        return $this;
    }

    public function endFieldset(): self
    {
        if ($this->form !== null && $this->fieldset !== null) {
            $this->form->append($this->fieldset->renderClose());
            $this->fieldset = null;
        }

        return $this;
    }

    public function addHtml(string $html): self
    {
        $this->assertFormOpen();
        $this->form?->append($html);

        return $this;
    }

    public function addInput(string $label, string $type = 'text', array $attributes = []): self
    {
        return $this->addElement(new InputElement($label, $type, $attributes), fillDefaultValue: !in_array($type, ['button', 'submit', 'reset', 'password'], true));
    }

    public function addTextarea(string $label, array $attributes = []): self
    {
        return $this->addElement(new TextareaElement($label, $attributes));
    }

    public function addEditor(string $label, array $attributes = []): self
    {
        $class = trim((string) ($attributes['class'] ?? ''));
        $attributes['class'] = trim($class . ' editor');

        return $this->addTextarea($label, $attributes);
    }

    public function addDatalist(string $label, array $options = [], array $attributes = []): self
    {
        return $this->addElement(new DatalistElement($label, $options, $attributes));
    }

    public function addCheckbox(string $label, array $options = [], array $attributes = []): self
    {
        return $this->addElement(new ChoiceElement($label, $options, 'checkbox', $attributes));
    }

    public function addRadio(string $label, array $options = [], array $attributes = []): self
    {
        return $this->addElement(new ChoiceElement($label, $options, 'radio', $attributes));
    }

    public function addYesNo(string $label, array $attributes = []): self
    {
        return $this->addRadio($label, ['1' => 'Yes', '0' => 'No'], $attributes);
    }

    public function addSelect(string $label, array $options = [], array $attributes = []): self
    {
        return $this->addElement(new SelectElement($label, $options, $attributes));
    }

    public function addCaptcha(string $label = 'Security Code', array $attributes = []): self
    {
        $element = new CaptchaElement($label, $attributes);

        return $this->addElement($element, fillDefaultValue: false);
    }

    public function addFile(string $label, array $attributes = []): self
    {
        return $this->addElement(new FileElement($label, $attributes), fillDefaultValue: false);
    }

    public function addConfiguredElement(ElementInterface $element, bool $fillDefaultValue = true): self
    {
        return $this->addElement($element, $fillDefaultValue);
    }

    private function addElement(ElementInterface $element, bool $fillDefaultValue = true): self
    {
        $this->assertFormOpen();

        if ($fillDefaultValue) {
            $default = $this->request->get($element->name(), $element->attributes()->get('value'));
            if ($element->attributes()->get('type') === 'password') {
                $default = '';
            }
            $element->setValue($default);
        }

        if ($element->shouldForceMultipart()) {
            $this->form?->attributes()->set('enctype', 'multipart/form-data');
        }

        $this->form?->append($element);

        return $this;
    }

    private function assertFormOpen(): void
    {
        if ($this->form === null) {
            throw new \LogicException('No form is currently open. Call open() first.');
        }
    }
}

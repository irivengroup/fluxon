<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator;

use Iriven\PhpFormGenerator\Application\FormBuilder;
use Iriven\PhpFormGenerator\Domain\Element\FileElement;
use Iriven\PhpFormGenerator\Domain\Element\InputElement;
use Iriven\PhpFormGenerator\Domain\Element\SelectElement;
use Iriven\PhpFormGenerator\Infrastructure\Request\ArrayRequestDataProvider;
use Iriven\PhpFormGenerator\Infrastructure\Request\RequestDataProviderInterface;

final class FormGenerator
{
    private FormBuilder $builder;

    public function __construct(?RequestDataProviderInterface $request = null)
    {
        $this->builder = new FormBuilder($request ?? ArrayRequestDataProvider::fromGlobals());
    }

    public function open(array $attributes = []): self
    {
        $this->builder->open($attributes);

        return $this;
    }

    public function close(): string
    {
        return $this->builder->close();
    }

    public function addFieldset(array $attributes = []): self
    {
        $this->builder->addFieldset($attributes);

        return $this;
    }

    public function endFieldset(): self
    {
        $this->builder->endFieldset();

        return $this;
    }

    public function addHtml(string $html): self
    {
        $this->builder->addHtml($html);

        return $this;
    }

    public function addAudio(string $label, array $attributes = []): self
    {
        $attributes['accept'] = 'audio/*';
        $this->builder->addFile($label, $attributes);

        return $this;
    }

    public function addButton(string $label, array $attributes = []): self
    {
        $attributes['value'] = $attributes['value'] ?? $label;
        $this->builder->addInput($label, 'button', $attributes);

        return $this;
    }

    public function addCaptcha(string $label = 'Security Code', array $attributes = []): self
    {
        $this->builder->addCaptcha($label, $attributes);

        return $this;
    }

    public function addCheckbox(string $label, array $options = [], array $attributes = []): self
    {
        $this->builder->addCheckbox($label, $options, $attributes);

        return $this;
    }

    public function addColor(string $label, array $attributes = []): self
    {
        $attributes += [
            'title' => '6-digit hexadecimal color (e.g. #000000)',
            'pattern' => '#[a-fA-F0-9]{6}',
        ];
        $this->builder->addInput($label, 'color', $attributes);

        return $this;
    }

    public function addCountries(string $label, array $attributes = []): self
    {
        $this->builder->addConfiguredElement(new Domain\Element\CountryElement($label, $attributes));

        return $this;
    }

    public function addDatalist(string $label, array $options = [], array $attributes = []): self
    {
        $this->builder->addDatalist($label, $options, $attributes);

        return $this;
    }

    public function addDate(string $label, array $attributes = []): self
    {
        $attributes += ['title' => 'YYYY-MM-DD'];
        $this->builder->addInput($label, 'date', $attributes);

        return $this;
    }

    public function addDatetime(string $label, array $attributes = []): self
    {
        $attributes += ['placeholder' => 'YYYY-MM-DD H:i:s'];
        $this->builder->addInput($label, 'datetime', $attributes);

        return $this;
    }

    public function addDatetimeLocal(string $label, array $attributes = []): self
    {
        $attributes += [
            'pattern' => '[0-9]{4}-[0-9]{2}-[0-9]{2}T[0-9]{2}:[0-9]{2}',
            'placeholder' => 'YYYY-MM-DDTHH:ii',
        ];
        $this->builder->addInput($label, 'datetime-local', $attributes);

        return $this;
    }

    public function addEditor(string $label, array $attributes = []): self
    {
        $this->builder->addEditor($label, $attributes);

        return $this;
    }

    public function addEmail(string $label, array $attributes = []): self
    {
        $attributes += [
            'placeholder' => 'mail@domain.com',
            'pattern' => '[A-Za-z0-9._%+-]{3,}@[a-zA-Z]{3,}([.]{1}[a-zA-Z]{2,}|[.]{1}[a-zA-Z]{2,}[.]{1}[a-zA-Z]{2,})',
        ];
        $this->builder->addInput($label, 'email', $attributes);

        return $this;
    }

    public function addFile(string $label, array $attributes = []): self
    {
        $this->builder->addFile($label, $attributes);

        return $this;
    }

    public function addHidden(string $label, array $attributes = []): self
    {
        $this->builder->addInput($label, 'hidden', $attributes);

        return $this;
    }

    public function addImage(string $label, array $attributes = []): self
    {
        $attributes['accept'] = 'image/*';
        $this->builder->addFile($label, $attributes);

        return $this;
    }

    public function addMonth(string $label, array $attributes = []): self
    {
        $attributes += [
            'pattern' => '\\d{4}-\\d{2}',
            'placeholder' => 'YYYY-MM',
        ];
        $this->builder->addInput($label, 'month', $attributes);

        return $this;
    }

    public function addNumber(string $label, array $attributes = []): self
    {
        $attributes += ['min' => '0', 'max' => '100', 'step' => '1'];
        $this->builder->addInput($label, 'number', $attributes);

        return $this;
    }

    public function addPassword(string $label, array $attributes = []): self
    {
        $attributes += [
            'minlength' => '6',
            'maxlength' => '64',
            'required' => 'required',
            'placeholder' => '**********',
            'value' => '',
        ];
        $this->builder->addInput($label, 'password', $attributes);

        return $this;
    }

    public function addPhone(string $label, array $attributes = []): self
    {
        $this->builder->addInput($label, 'tel', $attributes);

        return $this;
    }

    public function addRadio(string $label, array $options = [], array $attributes = []): self
    {
        $this->builder->addRadio($label, $options, $attributes);

        return $this;
    }

    public function addRange(string $label, array $attributes = []): self
    {
        $attributes += ['min' => '0', 'max' => '100', 'step' => '1'];
        $this->builder->addInput($label, 'range', $attributes);

        return $this;
    }

    public function addReset(string $label, array $attributes = []): self
    {
        $attributes['value'] = $attributes['value'] ?? $label;
        $this->builder->addInput($label, 'reset', $attributes);

        return $this;
    }

    public function addSearch(string $label, array $attributes = []): self
    {
        $attributes += ['required' => 'required'];
        $this->builder->addInput($label, 'search', $attributes);

        return $this;
    }

    public function addSelect(string $label, array $options = [], array $attributes = []): self
    {
        $this->builder->addSelect($label, $options, $attributes);

        return $this;
    }

    public function addSubmit(string $label, array $attributes = []): self
    {
        $attributes['value'] = $attributes['value'] ?? $label;
        $this->builder->addInput($label, 'submit', $attributes);

        return $this;
    }

    public function addText(string $label, array $attributes = []): self
    {
        $this->builder->addInput($label, 'text', $attributes);

        return $this;
    }

    public function addTextarea(string $label, array $attributes = []): self
    {
        $this->builder->addTextarea($label, $attributes);

        return $this;
    }

    public function addTime(string $label, array $attributes = []): self
    {
        $this->builder->addInput($label, 'time', $attributes);

        return $this;
    }

    public function addUrl(string $label, array $attributes = []): self
    {
        $this->builder->addInput($label, 'url', $attributes);

        return $this;
    }

    public function addVideo(string $label, array $attributes = []): self
    {
        $attributes['accept'] = 'video/*';
        $this->builder->addFile($label, $attributes);

        return $this;
    }

    public function addWeek(string $label, array $attributes = []): self
    {
        $this->builder->addInput($label, 'week', $attributes);

        return $this;
    }

    public function addYesNo(string $label, array $attributes = []): self
    {
        $this->builder->addYesNo($label, $attributes);

        return $this;
    }
}

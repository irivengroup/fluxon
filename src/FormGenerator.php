<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator;

use Iriven\PhpFormGenerator\Application\FormBuilder;
use Iriven\PhpFormGenerator\Infrastructure\PhpGlobalsRequestDataProvider;
use Iriven\PhpFormGenerator\Infrastructure\RequestDataProviderInterface;

final class FormGenerator
{
    private FormBuilder $builder;

    public function __construct(?RequestDataProviderInterface $requestDataProvider = null)
    {
        $this->builder = new FormBuilder($requestDataProvider ?? new PhpGlobalsRequestDataProvider());
    }

    public function open(array $attributes = []): self { $this->builder->open($attributes); return $this; }
    public function addFieldset(array $attributes = []): self { $this->builder->addFieldset($attributes); return $this; }
    public function endFieldset(): self { $this->builder->endFieldset(); return $this; }
    public function addHtml(string $html): self { $this->builder->addHtml($html); return $this; }
    public function addAudio(string $label, array $attributes = []): self { $this->builder->addAudio($label, $attributes); return $this; }
    public function addButton(string $label, array $attributes = []): self { $this->builder->addButton($label, $attributes); return $this; }
    public function addCaptcha(string $label = 'Security Code', array $attributes = []): self { $this->builder->addCaptcha($label, $attributes); return $this; }
    public function addCheckbox(string $label, array $options = [], array $attributes = []): self { $this->builder->addCheckbox($label, $options, $attributes); return $this; }
    public function addColor(string $label, array $attributes = []): self { $this->builder->addColor($label, $attributes); return $this; }
    public function addCountries(string $label, array $attributes = []): self { $this->builder->addCountries($label, $attributes); return $this; }
    public function addDatalist(string $label, array $options = [], array $attributes = []): self { $this->builder->addDatalist($label, $options, $attributes); return $this; }
    public function addDate(string $label, array $attributes = []): self { $this->builder->addDate($label, $attributes); return $this; }
    public function addDatetime(string $label, array $attributes = []): self { $this->builder->addDatetime($label, $attributes); return $this; }
    public function addDatetimeLocal(string $label, array $attributes = []): self { $this->builder->addDatetimeLocal($label, $attributes); return $this; }
    public function addEditor(string $label, array $attributes = []): self { $this->builder->addEditor($label, $attributes); return $this; }
    public function addEmail(string $label, array $attributes = []): self { $this->builder->addEmail($label, $attributes); return $this; }
    public function addFile(string $label, array $attributes = []): self { $this->builder->addFile($label, $attributes); return $this; }
    public function addHidden(string $label, array $attributes = []): self { $this->builder->addHidden($label, $attributes); return $this; }
    public function addImage(string $label, array $attributes = []): self { $this->builder->addImage($label, $attributes); return $this; }
    public function addMonth(string $label, array $attributes = []): self { $this->builder->addMonth($label, $attributes); return $this; }
    public function addNumber(string $label, array $attributes = []): self { $this->builder->addNumber($label, $attributes); return $this; }
    public function addPassword(string $label, array $attributes = []): self { $this->builder->addPassword($label, $attributes); return $this; }
    public function addPhone(string $label, array $attributes = []): self { $this->builder->addPhone($label, $attributes); return $this; }
    public function addRadio(string $label, array $options = [], array $attributes = []): self { $this->builder->addRadio($label, $options, $attributes); return $this; }
    public function addRange(string $label, array $attributes = []): self { $this->builder->addRange($label, $attributes); return $this; }
    public function addReset(string $label, array $attributes = []): self { $this->builder->addReset($label, $attributes); return $this; }
    public function addSearch(string $label, array $attributes = []): self { $this->builder->addSearch($label, $attributes); return $this; }
    public function addSelect(string $label, array $options = [], array $attributes = []): self { $this->builder->addSelect($label, $options, $attributes); return $this; }
    public function addSubmit(string $label, array $attributes = []): self { $this->builder->addSubmit($label, $attributes); return $this; }
    public function addText(string $label, array $attributes = []): self { $this->builder->addText($label, $attributes); return $this; }
    public function addTextarea(string $label, array $attributes = []): self { $this->builder->addTextarea($label, $attributes); return $this; }
    public function addTime(string $label, array $attributes = []): self { $this->builder->addTime($label, $attributes); return $this; }
    public function addUrl(string $label, array $attributes = []): self { $this->builder->addUrl($label, $attributes); return $this; }
    public function addVideo(string $label, array $attributes = []): self { $this->builder->addVideo($label, $attributes); return $this; }
    public function addWeek(string $label, array $attributes = []): self { $this->builder->addWeek($label, $attributes); return $this; }
    public function addYesNo(string $label, array $attributes = []): self { $this->builder->addYesNo($label, $attributes); return $this; }
    public function close(): string { return $this->builder->close(); }
}

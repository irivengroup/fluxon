<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Application;

use Iriven\PhpFormGenerator\Domain\Form\Form;
use Iriven\PhpFormGenerator\Domain\Form\FormBuilder;

final class FormGenerator
{
    private FormBuilder $builder;
    private FormGeneratorFieldFacade $fieldFacade;
    private FormGeneratorOpenNormalizer $openNormalizer;

    public function __construct(string $name = 'form')
    {
        $this->builder = new FormBuilder($name);
        $this->fieldFacade = new FormGeneratorFieldFacade($this->builder);
        $this->openNormalizer = new FormGeneratorOpenNormalizer();
    }

    /**
     * @param array<string, mixed> $attributes
     * @param array<string, mixed> $options
     */
    public function open(array $attributes = [], array $options = []): self
    {
        [$formAttributes, $configurationOptions] = $this->openNormalizer->normalize($attributes, $options);
        $configurationOptions['csrf_protection'] = $configurationOptions['csrf_protection'] ?? true;
        $this->builder->mergeOptions($configurationOptions + ['attr' => $formAttributes]);

        return $this;
    }

    /** @param array<string, mixed> $attributes */
    public function addText(string $name = 'name', array $attributes = []): self { $this->fieldFacade->addText($name, $attributes); return $this; }
    /** @param array<string, mixed> $attributes */
    public function addEmail(string $name, array $attributes = []): self { $this->fieldFacade->addEmail($name, $attributes); return $this; }
    /** @param array<string, mixed> $attributes */
    public function addTextarea(string $name, array $attributes = []): self { $this->fieldFacade->addTextarea($name, $attributes); return $this; }
    /** @param array<string, mixed> $attributes */
    public function addEditor(string $name, array $attributes = []): self { $this->fieldFacade->addEditor($name, $attributes); return $this; }
    /** @param array<string, mixed> $attributes */
    public function addHidden(string $name, array $attributes = []): self { $this->fieldFacade->addHidden($name, $attributes); return $this; }
    /** @param array<string, mixed> $attributes */
    public function addSubmit(string $name = 'submit', array $attributes = []): self { $this->fieldFacade->addSubmit($name, $attributes); return $this; }
    /** @param array<string, mixed> $attributes */
    public function addButton(string $name, array $attributes = []): self { $this->fieldFacade->addButton($name, $attributes); return $this; }
    /** @param array<string, mixed> $attributes */
    public function addReset(string $name, array $attributes = []): self { $this->fieldFacade->addReset($name, $attributes); return $this; }
    /** @param array<string, mixed> $attributes */
    public function addFile(string $name, array $attributes = []): self { $this->fieldFacade->addFile($name, $attributes); return $this; }
    /** @param array<string, mixed> $attributes */
    public function addAudio(string $name, array $attributes = []): self { $this->fieldFacade->addAudio($name, $attributes); return $this; }
    /** @param array<string, mixed> $attributes */
    public function addImage(string $name, array $attributes = []): self { $this->fieldFacade->addImage($name, $attributes); return $this; }
    /** @param array<string, mixed> $attributes */
    public function addVideo(string $name, array $attributes = []): self { $this->fieldFacade->addVideo($name, $attributes); return $this; }
    /** @param array<string, mixed> $attributes */
    public function addCountries(string $name, array $attributes = []): self { $this->fieldFacade->addCountries($name, $attributes); return $this; }
    /** @param array<string, mixed> $attributes */
    public function addCountry(string $name, array $attributes = []): self { $this->fieldFacade->addCountry($name, $attributes); return $this; }
    /** @param array<string, mixed> $attributes */
    public function addDatetime(string $name, array $attributes = []): self { $this->fieldFacade->addDatetime($name, $attributes); return $this; }
    /** @param array<string, mixed> $attributes */
    public function addDatetimeLocal(string $name, array $attributes = []): self { $this->fieldFacade->addDatetimeLocal($name, $attributes); return $this; }
    /** @param array<string, mixed> $attributes */
    public function addDate(string $name, array $attributes = []): self { $this->fieldFacade->addDate($name, $attributes); return $this; }
    /** @param array<string, mixed> $attributes */
    public function addTime(string $name, array $attributes = []): self { $this->fieldFacade->addTime($name, $attributes); return $this; }
    /** @param array<string, mixed> $attributes */
    public function addMonth(string $name, array $attributes = []): self { $this->fieldFacade->addMonth($name, $attributes); return $this; }
    /** @param array<string, mixed> $attributes */
    public function addWeek(string $name, array $attributes = []): self { $this->fieldFacade->addWeek($name, $attributes); return $this; }
    /** @param array<string, mixed> $attributes */
    public function addInteger(string $name, array $attributes = []): self { $this->fieldFacade->addInteger($name, $attributes); return $this; }
    /** @param array<string, mixed> $attributes */
    public function addFloat(string $name, array $attributes = []): self { $this->fieldFacade->addFloat($name, $attributes); return $this; }
    /** @param array<string, mixed> $attributes */
    public function addNumber(string $name, array $attributes = []): self { $this->fieldFacade->addNumber($name, $attributes); return $this; }
    /** @param array<string, mixed> $attributes */
    public function addRange(string $name, array $attributes = []): self { $this->fieldFacade->addRange($name, $attributes); return $this; }
    /** @param array<string, mixed> $attributes */
    public function addColor(string $name, array $attributes = []): self { $this->fieldFacade->addColor($name, $attributes); return $this; }
    /** @param array<string, mixed> $attributes */
    public function addPassword(string $name, array $attributes = []): self { $this->fieldFacade->addPassword($name, $attributes); return $this; }
    /** @param array<string, mixed> $attributes */
    public function addPhone(string $name, array $attributes = []): self { $this->fieldFacade->addPhone($name, $attributes); return $this; }
    /** @param array<string, mixed> $attributes */
    public function addSearch(string $name, array $attributes = []): self { $this->fieldFacade->addSearch($name, $attributes); return $this; }
    /** @param array<string, mixed> $attributes */
    public function addUrl(string $name, array $attributes = []): self { $this->fieldFacade->addUrl($name, $attributes); return $this; }
    /**
     * @param array<string, mixed> $choices
     * @param array<string, mixed> $attributes
     */
    public function addRadio(string $name, array $choices = [], array $attributes = []): self { $this->fieldFacade->addRadio($name, $choices, $attributes); return $this; }
    /**
     * @param array<string, mixed> $choices
     * @param array<string, mixed> $attributes
     */
    public function addCheckbox(string $name, array $choices = [], array $attributes = []): self { $this->fieldFacade->addCheckbox($name, $choices, $attributes); return $this; }
    /**
     * @param array<string, mixed> $choices
     * @param array<string, mixed> $attributes
     */
    public function addSelect(string $name, array $choices = [], array $attributes = []): self { $this->fieldFacade->addSelect($name, $choices, $attributes); return $this; }
    /** @param array<string, mixed> $attributes */
    public function addYesNo(string $name, array $attributes = []): self { $this->fieldFacade->addYesNo($name, $attributes); return $this; }
    /**
     * @param array<string, mixed> $choices
     * @param array<string, mixed> $attributes
     */
    public function addDatalist(string $name, array $choices = [], array $attributes = []): self { $this->fieldFacade->addDatalist($name, $choices, $attributes); return $this; }
    /** @param array<string, mixed> $attributes */
    public function addCaptcha(string $name, array $attributes = []): self { $this->fieldFacade->addCaptcha($name, $attributes); return $this; }
    /** @param array<string, mixed> $attributes */
    public function addCollection(string $name, array $attributes = []): self { $this->fieldFacade->addCollection($name, $attributes); return $this; }
    /** @param array<string, mixed> $attributes */
    public function addFieldset(array $attributes = []): self { $this->builder->addFieldset($attributes); return $this; }

    public function endFieldset(): self
    {
        $this->builder->endFieldset();

        return $this;
    }

    /** @param array<string, mixed> $attributes */
    public function add(string $name, string $typeClass, array $attributes = []): self
    {
        $this->fieldFacade->add($name, $typeClass, $attributes);

        return $this;
    }

    public function getForm(): Form
    {
        return $this->builder->getForm();
    }
}

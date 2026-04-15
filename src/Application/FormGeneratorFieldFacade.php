<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Application;

use Iriven\PhpFormGenerator\Domain\Field\AudioType;
use Iriven\PhpFormGenerator\Domain\Field\ButtonType;
use Iriven\PhpFormGenerator\Domain\Field\CaptchaType;
use Iriven\PhpFormGenerator\Domain\Field\CheckboxType;
use Iriven\PhpFormGenerator\Domain\Field\CollectionType;
use Iriven\PhpFormGenerator\Domain\Field\ColorType;
use Iriven\PhpFormGenerator\Domain\Field\CountryType;
use Iriven\PhpFormGenerator\Domain\Field\DatalistType;
use Iriven\PhpFormGenerator\Domain\Field\DateType;
use Iriven\PhpFormGenerator\Domain\Field\DatetimeLocalType;
use Iriven\PhpFormGenerator\Domain\Field\DatetimeType;
use Iriven\PhpFormGenerator\Domain\Field\EditorType;
use Iriven\PhpFormGenerator\Domain\Field\EmailType;
use Iriven\PhpFormGenerator\Domain\Field\FileType;
use Iriven\PhpFormGenerator\Domain\Field\FloatType;
use Iriven\PhpFormGenerator\Domain\Field\HiddenType;
use Iriven\PhpFormGenerator\Domain\Field\ImageType;
use Iriven\PhpFormGenerator\Domain\Field\IntegerType;
use Iriven\PhpFormGenerator\Domain\Field\MonthType;
use Iriven\PhpFormGenerator\Domain\Field\NumberType;
use Iriven\PhpFormGenerator\Domain\Field\PasswordType;
use Iriven\PhpFormGenerator\Domain\Field\PhoneType;
use Iriven\PhpFormGenerator\Domain\Field\RadioType;
use Iriven\PhpFormGenerator\Domain\Field\RangeType;
use Iriven\PhpFormGenerator\Domain\Field\ResetType;
use Iriven\PhpFormGenerator\Domain\Field\SearchType;
use Iriven\PhpFormGenerator\Domain\Field\SelectType;
use Iriven\PhpFormGenerator\Domain\Field\SubmitType;
use Iriven\PhpFormGenerator\Domain\Field\TextareaType;
use Iriven\PhpFormGenerator\Domain\Field\TextType;
use Iriven\PhpFormGenerator\Domain\Field\TimeType;
use Iriven\PhpFormGenerator\Domain\Field\UrlType;
use Iriven\PhpFormGenerator\Domain\Field\VideoType;
use Iriven\PhpFormGenerator\Domain\Field\WeekType;
use Iriven\PhpFormGenerator\Domain\Field\YesNoType;
use Iriven\PhpFormGenerator\Domain\Form\FormBuilder;

final class FormGeneratorFieldFacade
{
    public function __construct(private readonly FormBuilder $builder)
    {
    }

    /** @param array<string, mixed> $attributes */
    public function addText(string $name, array $attributes = []): void { $this->add($name, TextType::class, $attributes); }
    /** @param array<string, mixed> $attributes */
    public function addEmail(string $name, array $attributes = []): void { $this->add($name, EmailType::class, $attributes); }
    /** @param array<string, mixed> $attributes */
    public function addTextarea(string $name, array $attributes = []): void { $this->add($name, TextareaType::class, $attributes); }
    /** @param array<string, mixed> $attributes */
    public function addEditor(string $name, array $attributes = []): void { $this->add($name, EditorType::class, $attributes); }
    /** @param array<string, mixed> $attributes */
    public function addHidden(string $name, array $attributes = []): void { $this->add($name, HiddenType::class, $attributes); }
    /** @param array<string, mixed> $attributes */
    public function addSubmit(string $name, array $attributes = []): void { $this->add($name, SubmitType::class, $attributes); }
    /** @param array<string, mixed> $attributes */
    public function addButton(string $name, array $attributes = []): void { $this->add($name, ButtonType::class, $attributes); }
    /** @param array<string, mixed> $attributes */
    public function addReset(string $name, array $attributes = []): void { $this->add($name, ResetType::class, $attributes); }
    /** @param array<string, mixed> $attributes */
    public function addFile(string $name, array $attributes = []): void { $this->add($name, FileType::class, $attributes); }
    /** @param array<string, mixed> $attributes */
    public function addAudio(string $name, array $attributes = []): void { $this->add($name, AudioType::class, $attributes); }
    /** @param array<string, mixed> $attributes */
    public function addImage(string $name, array $attributes = []): void { $this->add($name, ImageType::class, $attributes); }
    /** @param array<string, mixed> $attributes */
    public function addVideo(string $name, array $attributes = []): void { $this->add($name, VideoType::class, $attributes); }
    /** @param array<string, mixed> $attributes */
    public function addCountries(string $name, array $attributes = []): void { $this->add($name, CountryType::class, $attributes); }
    /** @param array<string, mixed> $attributes */
    public function addCountry(string $name, array $attributes = []): void { $this->addCountries($name, $attributes); }
    /** @param array<string, mixed> $attributes */
    public function addDatetime(string $name, array $attributes = []): void { $this->add($name, DatetimeType::class, $attributes); }
    /** @param array<string, mixed> $attributes */
    public function addDatetimeLocal(string $name, array $attributes = []): void { $this->add($name, DatetimeLocalType::class, $attributes); }
    /** @param array<string, mixed> $attributes */
    public function addDate(string $name, array $attributes = []): void { $this->add($name, DateType::class, $attributes); }
    /** @param array<string, mixed> $attributes */
    public function addTime(string $name, array $attributes = []): void { $this->add($name, TimeType::class, $attributes); }
    /** @param array<string, mixed> $attributes */
    public function addMonth(string $name, array $attributes = []): void { $this->add($name, MonthType::class, $attributes); }
    /** @param array<string, mixed> $attributes */
    public function addWeek(string $name, array $attributes = []): void { $this->add($name, WeekType::class, $attributes); }
    /** @param array<string, mixed> $attributes */
    public function addInteger(string $name, array $attributes = []): void { $this->add($name, IntegerType::class, $attributes); }
    /** @param array<string, mixed> $attributes */
    public function addFloat(string $name, array $attributes = []): void { $this->add($name, FloatType::class, $attributes); }
    /** @param array<string, mixed> $attributes */
    public function addNumber(string $name, array $attributes = []): void { $this->add($name, NumberType::class, $attributes); }
    /** @param array<string, mixed> $attributes */
    public function addRange(string $name, array $attributes = []): void { $this->add($name, RangeType::class, $attributes); }
    /** @param array<string, mixed> $attributes */
    public function addColor(string $name, array $attributes = []): void { $this->add($name, ColorType::class, $attributes); }
    /** @param array<string, mixed> $attributes */
    public function addPassword(string $name, array $attributes = []): void { $this->add($name, PasswordType::class, $attributes); }
    /** @param array<string, mixed> $attributes */
    public function addPhone(string $name, array $attributes = []): void { $this->add($name, PhoneType::class, $attributes); }
    /** @param array<string, mixed> $attributes */
    public function addSearch(string $name, array $attributes = []): void { $this->add($name, SearchType::class, $attributes); }
    /** @param array<string, mixed> $attributes */
    public function addUrl(string $name, array $attributes = []): void { $this->add($name, UrlType::class, $attributes); }
    /**
     * @param array<string, mixed> $choices
     * @param array<string, mixed> $attributes
     */
    public function addRadio(string $name, array $choices = [], array $attributes = []): void { $this->addChoiceField($name, RadioType::class, $choices, $attributes); }
    /**
     * @param array<string, mixed> $choices
     * @param array<string, mixed> $attributes
     */
    public function addCheckbox(string $name, array $choices = [], array $attributes = []): void { $this->addChoiceField($name, CheckboxType::class, $choices, $attributes); }
    /**
     * @param array<string, mixed> $choices
     * @param array<string, mixed> $attributes
     */
    public function addSelect(string $name, array $choices = [], array $attributes = []): void { $this->addChoiceField($name, SelectType::class, $choices, $attributes); }
    /** @param array<string, mixed> $attributes */
    public function addYesNo(string $name, array $attributes = []): void { $this->add($name, YesNoType::class, $attributes); }
    /**
     * @param array<string, mixed> $choices
     * @param array<string, mixed> $attributes
     */
    public function addDatalist(string $name, array $choices = [], array $attributes = []): void { $this->addChoiceField($name, DatalistType::class, $choices, $attributes); }
    /** @param array<string, mixed> $attributes */
    public function addCaptcha(string $name, array $attributes = []): void { $this->add($name, CaptchaType::class, $attributes); }
    /** @param array<string, mixed> $attributes */
    public function addCollection(string $name, array $attributes = []): void { $this->add($name, CollectionType::class, $attributes); }

    /** @param array<string, mixed> $attributes */
    public function add(string $name, string $typeClass, array $attributes = []): void
    {
        $this->builder->add($name, $typeClass, $this->normalizeFieldAttributes($attributes));
    }

    /**
     * @param array<string, mixed> $choices
     * @param array<string, mixed> $attributes
     */
    private function addChoiceField(string $name, string $typeClass, array $choices, array $attributes): void
    {
        if ($choices !== []) {
            $attributes['choices'] = $choices;
        }

        $this->add($name, $typeClass, $attributes);
    }

    /**
     * @param array<string, mixed> $attributes
     * @return array<string, mixed>
     */
    private function normalizeFieldAttributes(array $attributes): array
    {
        if (isset($attributes['attributes']) && is_array($attributes['attributes'])) {
            $htmlAttributes = is_array($attributes['attr'] ?? null) ? $attributes['attr'] : [];
            $attributes['attr'] = array_replace($htmlAttributes, $attributes['attributes']);
            unset($attributes['attributes']);
        }

        $htmlAttributeKeys = [
            'class', 'id', 'style', 'placeholder', 'autocomplete', 'autocapitalize', 'spellcheck',
            'rows', 'cols', 'min', 'max', 'step', 'pattern', 'accept', 'multiple', 'readonly',
            'disabled', 'size', 'maxlength', 'minlength', 'inputmode', 'list',
        ];

        $htmlAttributes = is_array($attributes['attr'] ?? null) ? $attributes['attr'] : [];
        foreach ($htmlAttributeKeys as $key) {
            if (array_key_exists($key, $attributes)) {
                $htmlAttributes[$key] = $attributes[$key];
                unset($attributes[$key]);
            }
        }

        if ($htmlAttributes !== []) {
            $attributes['attr'] = $htmlAttributes;
        }

        return $attributes;
    }
}

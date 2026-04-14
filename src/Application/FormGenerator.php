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
use Iriven\PhpFormGenerator\Domain\Field\DatetimeLocalType;
use Iriven\PhpFormGenerator\Domain\Field\DatetimeType;
use Iriven\PhpFormGenerator\Domain\Field\DateType;
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
use Iriven\PhpFormGenerator\Domain\Form\Form;
use Iriven\PhpFormGenerator\Domain\Form\FormBuilder;

final class FormGenerator
{
    private FormBuilder $builder;

    public function __construct(string $name = 'form')
    {
        $this->builder = new FormBuilder($name);
    }

    /** @param array<string, mixed> $options */
    public function open(array $options = []): self
    {
        $options['csrf_protection'] = $options['csrf_protection'] ?? true;
        $this->builder->mergeOptions($options);

        return $this;
    }

    /** @param array<string, mixed> $options */
    public function addText(string $name, array $options = []): self { return $this->add($name, TextType::class, $options); }
    /** @param array<string, mixed> $options */
    public function addEmail(string $name, array $options = []): self { return $this->add($name, EmailType::class, $options); }
    /** @param array<string, mixed> $options */
    public function addTextarea(string $name, array $options = []): self { return $this->add($name, TextareaType::class, $options); }
    /** @param array<string, mixed> $options */
    public function addEditor(string $name, array $options = []): self { return $this->add($name, EditorType::class, $options); }
    /** @param array<string, mixed> $options */
    public function addCheckbox(string $name, array $options = []): self { return $this->add($name, CheckboxType::class, $options); }
    /** @param array<string, mixed> $options */
    public function addHidden(string $name, array $options = []): self { return $this->add($name, HiddenType::class, $options); }
    /** @param array<string, mixed> $options */
    public function addSubmit(string $name = 'submit', array $options = []): self { return $this->add($name, SubmitType::class, $options); }
    /** @param array<string, mixed> $options */
    public function addButton(string $name, array $options = []): self { return $this->add($name, ButtonType::class, $options); }
    /** @param array<string, mixed> $options */
    public function addReset(string $name, array $options = []): self { return $this->add($name, ResetType::class, $options); }
    /** @param array<string, mixed> $options */
    public function addFile(string $name, array $options = []): self { return $this->add($name, FileType::class, $options); }
    /** @param array<string, mixed> $options */
    public function addAudio(string $name, array $options = []): self { return $this->add($name, AudioType::class, $options); }
    /** @param array<string, mixed> $options */
    public function addImage(string $name, array $options = []): self { return $this->add($name, ImageType::class, $options); }
    /** @param array<string, mixed> $options */
    public function addVideo(string $name, array $options = []): self { return $this->add($name, VideoType::class, $options); }
    /** @param array<string, mixed> $options */
    public function addCountries(string $name, array $options = []): self { return $this->add($name, CountryType::class, $options); }
    /** @param array<string, mixed> $options */
    public function addCountry(string $name, array $options = []): self { return $this->addCountries($name, $options); }
    /** @param array<string, mixed> $options */
    public function addDatetime(string $name, array $options = []): self { return $this->add($name, DatetimeType::class, $options); }
    /** @param array<string, mixed> $options */
    public function addDatetimeLocal(string $name, array $options = []): self { return $this->add($name, DatetimeLocalType::class, $options); }
    /** @param array<string, mixed> $options */
    public function addDate(string $name, array $options = []): self { return $this->add($name, DateType::class, $options); }
    /** @param array<string, mixed> $options */
    public function addTime(string $name, array $options = []): self { return $this->add($name, TimeType::class, $options); }
    /** @param array<string, mixed> $options */
    public function addMonth(string $name, array $options = []): self { return $this->add($name, MonthType::class, $options); }
    /** @param array<string, mixed> $options */
    public function addWeek(string $name, array $options = []): self { return $this->add($name, WeekType::class, $options); }
    /** @param array<string, mixed> $options */
    public function addInteger(string $name, array $options = []): self { return $this->add($name, IntegerType::class, $options); }
    /** @param array<string, mixed> $options */
    public function addFloat(string $name, array $options = []): self { return $this->add($name, FloatType::class, $options); }
    /** @param array<string, mixed> $options */
    public function addNumber(string $name, array $options = []): self { return $this->add($name, NumberType::class, $options); }
    /** @param array<string, mixed> $options */
    public function addRange(string $name, array $options = []): self { return $this->add($name, RangeType::class, $options); }
    /** @param array<string, mixed> $options */
    public function addColor(string $name, array $options = []): self { return $this->add($name, ColorType::class, $options); }
    /** @param array<string, mixed> $options */
    public function addPassword(string $name, array $options = []): self { return $this->add($name, PasswordType::class, $options); }
    /** @param array<string, mixed> $options */
    public function addPhone(string $name, array $options = []): self { return $this->add($name, PhoneType::class, $options); }
    /** @param array<string, mixed> $options */
    public function addSearch(string $name, array $options = []): self { return $this->add($name, SearchType::class, $options); }
    /** @param array<string, mixed> $options */
    public function addUrl(string $name, array $options = []): self { return $this->add($name, UrlType::class, $options); }
    /** @param array<string, mixed> $options */
    public function addRadio(string $name, array $options = []): self { return $this->add($name, RadioType::class, $options); }
    /** @param array<string, mixed> $options */
    public function addSelect(string $name, array $options = []): self { return $this->add($name, SelectType::class, $options); }
    /** @param array<string, mixed> $options */
    public function addYesNo(string $name, array $options = []): self { return $this->add($name, YesNoType::class, $options); }
    /** @param array<string, mixed> $options */
    public function addDatalist(string $name, array $options = []): self { return $this->add($name, DatalistType::class, $options); }
    /** @param array<string, mixed> $options */
    public function addCaptcha(string $name, array $options = []): self { return $this->add($name, CaptchaType::class, $options); }
    /** @param array<string, mixed> $options */
    public function addCollection(string $name, array $options = []): self { return $this->add($name, CollectionType::class, $options); }
    /** @param array<string, mixed> $options */
    public function addFieldset(array $options = []): self { $this->builder->addFieldset($options); return $this; }
    public function endFieldset(): self { $this->builder->endFieldset(); return $this; }
    public function getForm(): Form { return $this->builder->getForm(); }

    /**
     * @param string $typeClass
     * @param array<string, mixed> $options
     */
    private function add(string $name, string $typeClass, array $options = []): self
    {
        $this->builder->add($name, $typeClass, $options);
        return $this;
    }
}

<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator;

use Iriven\PhpFormGenerator\Application\FormFactory;
use Iriven\PhpFormGenerator\Domain\Field\AudioType;
use Iriven\PhpFormGenerator\Domain\Field\ButtonType;
use Iriven\PhpFormGenerator\Domain\Field\CaptchaType;
use Iriven\PhpFormGenerator\Domain\Field\CheckboxType;
use Iriven\PhpFormGenerator\Domain\Field\CollectionType;
use Iriven\PhpFormGenerator\Domain\Field\ColorType;
use Iriven\PhpFormGenerator\Domain\Field\CountryType;
use Iriven\PhpFormGenerator\Domain\Field\DatalistType;
use Iriven\PhpFormGenerator\Domain\Field\DateTimeLocalType;
use Iriven\PhpFormGenerator\Domain\Field\DatetimeType;
use Iriven\PhpFormGenerator\Domain\Field\DateType;
use Iriven\PhpFormGenerator\Domain\Field\EditorType;
use Iriven\PhpFormGenerator\Domain\Field\EmailType;
use Iriven\PhpFormGenerator\Domain\Field\FileType;
use Iriven\PhpFormGenerator\Domain\Field\FormTypeField;
use Iriven\PhpFormGenerator\Domain\Field\HiddenType;
use Iriven\PhpFormGenerator\Domain\Field\HtmlType;
use Iriven\PhpFormGenerator\Domain\Field\ImageType;
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
use Iriven\PhpFormGenerator\Domain\Field\TelType;
use Iriven\PhpFormGenerator\Domain\Field\TextType;
use Iriven\PhpFormGenerator\Domain\Field\TextareaType;
use Iriven\PhpFormGenerator\Domain\Field\TimeType;
use Iriven\PhpFormGenerator\Domain\Field\UrlType;
use Iriven\PhpFormGenerator\Domain\Field\VideoType;
use Iriven\PhpFormGenerator\Domain\Field\WeekType;
use Iriven\PhpFormGenerator\Domain\Field\YesNoType;
use Iriven\PhpFormGenerator\Domain\Form\FormInterface;

final class FormGenerator
{
    private ?\Iriven\PhpFormGenerator\Domain\Form\FormBuilder $builder = null;
    private ?FormInterface $form = null;

    public function __construct(
        private readonly ?FormFactory $factory = null,
    ) {
    }

    public function open(string $name = 'form', mixed $data = [], array $options = []): self
    {
        $this->builder = ($this->factory ?? new FormFactory())->createBuilder($name, $data, $options);
        return $this;
    }

    public function getForm(): FormInterface
    {
        $this->form ??= $this->builder?->getForm();
        if ($this->form === null) {
            throw new \LogicException('Open a form before retrieving it.');
        }
        return $this->form;
    }

    public function add(string $name, string $type, array $options = []): self
    {
        $this->builder?->add($name, $type, $options);
        return $this;
    }

    public function addText(string $name, array $options = []): self { return $this->add($name, TextType::class, $options); }
    public function addEmail(string $name, array $options = []): self { return $this->add($name, EmailType::class, $options); }
    public function addPassword(string $name, array $options = []): self { return $this->add($name, PasswordType::class, $options); }
    public function addPhone(string $name, array $options = []): self { return $this->add($name, PhoneType::class, $options); }
    public function addUrl(string $name, array $options = []): self { return $this->add($name, UrlType::class, $options); }
    public function addSearch(string $name, array $options = []): self { return $this->add($name, SearchType::class, $options); }
    public function addNumber(string $name, array $options = []): self { return $this->add($name, NumberType::class, $options); }
    public function addRange(string $name, array $options = []): self { return $this->add($name, RangeType::class, $options); }
    public function addColor(string $name, array $options = []): self { return $this->add($name, ColorType::class, $options); }
    public function addDate(string $name, array $options = []): self { return $this->add($name, DateType::class, $options); }
    public function addDatetime(string $name, array $options = []): self { return $this->add($name, DatetimeType::class, $options); }
    public function addDatetimeLocal(string $name, array $options = []): self { return $this->add($name, DateTimeLocalType::class, $options); }
    public function addMonth(string $name, array $options = []): self { return $this->add($name, MonthType::class, $options); }
    public function addWeek(string $name, array $options = []): self { return $this->add($name, WeekType::class, $options); }
    public function addTime(string $name, array $options = []): self { return $this->add($name, TimeType::class, $options); }
    public function addTextarea(string $name, array $options = []): self { return $this->add($name, TextareaType::class, $options); }
    public function addHidden(string $name, array $options = []): self { return $this->add($name, HiddenType::class, $options); }
    public function addCheckbox(string $name, array $options = []): self { return $this->add($name, CheckboxType::class, $options); }
    public function addRadio(string $name, array $options = []): self { return $this->add($name, RadioType::class, $options); }
    public function addSelect(string $name, array $options = []): self { return $this->add($name, SelectType::class, $options); }
    public function addYesNo(string $name, array $options = []): self { return $this->add($name, YesNoType::class, $options); }
    public function addCountries(string $name, array $options = []): self { return $this->add($name, CountryType::class, $options); }
    public function addFile(string $name, array $options = []): self { return $this->add($name, FileType::class, $options); }
    public function addButton(string $name, array $options = []): self { return $this->add($name, ButtonType::class, $options); }
    public function addSubmit(string $name, array $options = []): self { return $this->add($name, SubmitType::class, $options); }
    public function addReset(string $name, array $options = []): self { return $this->add($name, ResetType::class, $options); }
    public function addDatalist(string $name, array $options = []): self { return $this->add($name, DatalistType::class, $options); }
    public function addEditor(string $name, array $options = []): self { return $this->add($name, EditorType::class, $options); }
    public function addCaptcha(string $name, array $options = []): self { return $this->add($name, CaptchaType::class, $options); }
    public function addAudio(string $name, array $options = []): self { return $this->add($name, AudioType::class, $options); }
    public function addVideo(string $name, array $options = []): self { return $this->add($name, VideoType::class, $options); }
    public function addImage(string $name, array $options = []): self { return $this->add($name, ImageType::class, $options); }
    public function addCollection(string $name, array $options = []): self { return $this->add($name, CollectionType::class, $options); }
    public function addNestedForm(string $name, array $options = []): self { return $this->add($name, FormTypeField::class, $options); }
    public function addHtml(string $name, array $options = []): self { return $this->add($name, HtmlType::class, $options); }
}

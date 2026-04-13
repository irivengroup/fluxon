<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Application;

use Iriven\PhpFormGenerator\Domain\Constraint\Email;
use Iriven\PhpFormGenerator\Domain\Constraint\Required;
use Iriven\PhpFormGenerator\Domain\Field\AudioType;
use Iriven\PhpFormGenerator\Domain\Field\ButtonType;
use Iriven\PhpFormGenerator\Domain\Field\CaptchaType;
use Iriven\PhpFormGenerator\Domain\Field\CheckboxType;
use Iriven\PhpFormGenerator\Domain\Field\ColorType;
use Iriven\PhpFormGenerator\Domain\Field\CountryType;
use Iriven\PhpFormGenerator\Domain\Field\DatalistType;
use Iriven\PhpFormGenerator\Domain\Field\DateType;
use Iriven\PhpFormGenerator\Domain\Field\DatetimeLocalType;
use Iriven\PhpFormGenerator\Domain\Field\DatetimeType;
use Iriven\PhpFormGenerator\Domain\Field\EditorType;
use Iriven\PhpFormGenerator\Domain\Field\EmailType;
use Iriven\PhpFormGenerator\Domain\Field\FileType;
use Iriven\PhpFormGenerator\Domain\Field\HiddenType;
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
use Iriven\PhpFormGenerator\Domain\Field\TextType;
use Iriven\PhpFormGenerator\Domain\Field\TextareaType;
use Iriven\PhpFormGenerator\Domain\Field\TimeType;
use Iriven\PhpFormGenerator\Domain\Field\UrlType;
use Iriven\PhpFormGenerator\Domain\Field\VideoType;
use Iriven\PhpFormGenerator\Domain\Field\WeekType;
use Iriven\PhpFormGenerator\Domain\Field\YesNoType;
use Iriven\PhpFormGenerator\Domain\Form\Form;
use Iriven\PhpFormGenerator\Presentation\Html\HtmlRenderer;

final class FormGenerator
{
    private ?\Iriven\PhpFormGenerator\Domain\Form\FormBuilder $builder = null;

    public function __construct(private readonly ?FormFactory $factory = null)
    {
    }

    public function open(string $name = 'form', array $options = []): self
    {
        $this->builder = ($this->factory ?? new FormFactory())->createBuilder($name, $options);
        return $this;
    }

    public function addFieldset(array $options = []): self
    {
        $this->builder()->addFieldset($options);
        return $this;
    }

    public function endFieldset(): self
    {
        $this->builder()->endFieldset();
        return $this;
    }

    public function addAudio(string $name, array $options = []): self
    {
        $this->builder()->add($name, AudioType::class, $options);
        return $this;
    }

    public function addButton(string $name, array $options = []): self
    {
        $this->builder()->add($name, ButtonType::class, $options);
        return $this;
    }

    public function addCaptcha(string $name = 'security_code', array $options = []): self
    {
        $this->builder()->add($name, CaptchaType::class, $options);
        return $this;
    }

    public function addCheckbox(string $name, array $choices = [], array $options = []): self
    {
        if ($choices !== []) {
            $options['choices'] = $choices;
        }
        $type = isset($options['choices']) ? SelectType::class : CheckboxType::class;
        $this->builder()->add($name, $type, $options);
        return $this;
    }

    public function addColor(string $name, array $options = []): self
    {
        $this->builder()->add($name, ColorType::class, $options);
        return $this;
    }

    public function addCountries(string $name, array $options = []): self
    {
        $this->builder()->add($name, CountryType::class, $options);
        return $this;
    }

    public function addDatalist(string $name, array $choices = [], array $options = []): self
    {
        if ($choices !== []) {
            $options['choices'] = $choices;
        }
        $this->builder()->add($name, DatalistType::class, $options);
        return $this;
    }

    public function addDate(string $name, array $options = []): self
    {
        $this->builder()->add($name, DateType::class, $options);
        return $this;
    }

    public function addDatetime(string $name, array $options = []): self
    {
        $this->builder()->add($name, DatetimeType::class, $options);
        return $this;
    }

    public function addDatetimeLocal(string $name, array $options = []): self
    {
        $this->builder()->add($name, DatetimeLocalType::class, $options);
        return $this;
    }

    public function addEditor(string $name, array $options = []): self
    {
        $this->builder()->add($name, EditorType::class, $options);
        return $this;
    }

    public function addEmail(string $name, array $options = []): self
    {
        $constraints = $options['constraints'] ?? [];
        $constraints[] = new Email();
        if (($options['required'] ?? false) === true) {
            $constraints[] = new Required();
        }
        $options['constraints'] = $constraints;
        $this->builder()->add($name, EmailType::class, $options);
        return $this;
    }

    public function addFile(string $name, array $options = []): self
    {
        $this->builder()->add($name, FileType::class, $options);
        return $this;
    }

    public function addHidden(string $name, array $options = []): self
    {
        $this->builder()->add($name, HiddenType::class, $options);
        return $this;
    }

    public function addImage(string $name, array $options = []): self
    {
        $this->builder()->add($name, ImageType::class, $options);
        return $this;
    }

    public function addMonth(string $name, array $options = []): self
    {
        $this->builder()->add($name, MonthType::class, $options);
        return $this;
    }

    public function addNumber(string $name, array $options = []): self
    {
        $this->builder()->add($name, NumberType::class, $options);
        return $this;
    }

    public function addPassword(string $name, array $options = []): self
    {
        $this->builder()->add($name, PasswordType::class, $options);
        return $this;
    }

    public function addPhone(string $name, array $options = []): self
    {
        $this->builder()->add($name, PhoneType::class, $options);
        return $this;
    }

    public function addRadio(string $name, array $choices = [], array $options = []): self
    {
        $options['choices'] = $choices;
        $this->builder()->add($name, RadioType::class, $options);
        return $this;
    }

    public function addRange(string $name, array $options = []): self
    {
        $this->builder()->add($name, RangeType::class, $options);
        return $this;
    }

    public function addReset(string $name = 'reset', array $options = []): self
    {
        $this->builder()->add($name, ResetType::class, $options);
        return $this;
    }

    public function addSearch(string $name, array $options = []): self
    {
        $this->builder()->add($name, SearchType::class, $options);
        return $this;
    }

    public function addSelect(string $name, array $choices = [], array $options = []): self
    {
        $options['choices'] = $choices;
        $this->builder()->add($name, SelectType::class, $options);
        return $this;
    }

    public function addSubmit(string $name = 'submit', array $options = []): self
    {
        $this->builder()->add($name, SubmitType::class, $options);
        return $this;
    }

    public function addText(string $name, array $options = []): self
    {
        $this->builder()->add($name, TextType::class, $options);
        return $this;
    }

    public function addTextarea(string $name, array $options = []): self
    {
        $this->builder()->add($name, TextareaType::class, $options);
        return $this;
    }

    public function addTime(string $name, array $options = []): self
    {
        $this->builder()->add($name, TimeType::class, $options);
        return $this;
    }

    public function addUrl(string $name, array $options = []): self
    {
        $this->builder()->add($name, UrlType::class, $options);
        return $this;
    }

    public function addVideo(string $name, array $options = []): self
    {
        $this->builder()->add($name, VideoType::class, $options);
        return $this;
    }

    public function addWeek(string $name, array $options = []): self
    {
        $this->builder()->add($name, WeekType::class, $options);
        return $this;
    }

    public function addYesNo(string $name, array $options = []): self
    {
        $this->builder()->add($name, YesNoType::class, $options);
        return $this;
    }

    public function getForm(): Form
    {
        return $this->builder()->getForm();
    }

    public function render(): string
    {
        return (new HtmlRenderer())->render($this->getForm());
    }

    private function builder(): \Iriven\PhpFormGenerator\Domain\Form\FormBuilder
    {
        if ($this->builder === null) {
            $this->open();
        }
        return $this->builder;
    }
}

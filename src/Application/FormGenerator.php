<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Application;

use Iriven\PhpFormGenerator\Domain\Constraint\Email;
use Iriven\PhpFormGenerator\Domain\Constraint\Required;
use Iriven\PhpFormGenerator\Domain\Field\CheckboxType;
use Iriven\PhpFormGenerator\Domain\Field\CountryType;
use Iriven\PhpFormGenerator\Domain\Field\EmailType;
use Iriven\PhpFormGenerator\Domain\Field\FileType;
use Iriven\PhpFormGenerator\Domain\Field\HiddenType;
use Iriven\PhpFormGenerator\Domain\Field\SubmitType;
use Iriven\PhpFormGenerator\Domain\Field\TextType;
use Iriven\PhpFormGenerator\Domain\Field\TextareaType;
use Iriven\PhpFormGenerator\Domain\Field\UrlType;
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
        $this->builder()?->addFieldset($options);
        return $this;
    }

    public function endFieldset(): self
    {
        $this->builder()?->endFieldset();
        return $this;
    }

    public function addText(string $name, array $options = []): self
    {
        $this->builder()?->add($name, TextType::class, $options);
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
        $this->builder()?->add($name, EmailType::class, $options);
        return $this;
    }

    public function addTextarea(string $name, array $options = []): self
    {
        $this->builder()?->add($name, TextareaType::class, $options);
        return $this;
    }

    public function addCheckbox(string $name, array $options = []): self
    {
        $this->builder()?->add($name, CheckboxType::class, $options);
        return $this;
    }

    public function addFile(string $name, array $options = []): self
    {
        $this->builder()?->add($name, FileType::class, $options);
        return $this;
    }

    public function addHidden(string $name, array $options = []): self
    {
        $this->builder()?->add($name, HiddenType::class, $options);
        return $this;
    }

    public function addCountries(string $name, array $options = []): self
    {
        $this->builder()?->add($name, CountryType::class, $options);
        return $this;
    }

    public function addYesNo(string $name, array $options = []): self
    {
        $this->builder()?->add($name, YesNoType::class, $options);
        return $this;
    }

    public function addUrl(string $name, array $options = []): self
    {
        $this->builder()?->add($name, UrlType::class, $options);
        return $this;
    }

    public function addSubmit(string $name = 'submit', array $options = []): self
    {
        $this->builder()?->add($name, SubmitType::class, $options);
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

<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Application;

use Iriven\PhpFormGenerator\Domain\Data\Countries;
use Iriven\PhpFormGenerator\Domain\Element\ChoiceElement;
use Iriven\PhpFormGenerator\Domain\Element\DatalistElement;
use Iriven\PhpFormGenerator\Domain\Element\InputElement;
use Iriven\PhpFormGenerator\Domain\Element\RawHtml;
use Iriven\PhpFormGenerator\Domain\Element\SelectElement;
use Iriven\PhpFormGenerator\Domain\Element\TextareaElement;
use Iriven\PhpFormGenerator\Domain\Fieldset;
use Iriven\PhpFormGenerator\Domain\Form;
use Iriven\PhpFormGenerator\Infrastructure\RequestDataProviderInterface;

final class FormBuilder
{
    private ?Form $form = null;
    private ?Fieldset $fieldset = null;

    public function __construct(private readonly RequestDataProviderInterface $requestDataProvider) {}

    public function open(array $attributes = []): self
    {
        $this->form = new Form($attributes);
        $this->fieldset = null;
        return $this;
    }

    public function addFieldset(array $attributes = []): self
    {
        $this->assertOpen();
        $this->flushFieldset();
        $this->fieldset = new Fieldset($attributes);
        return $this;
    }

    public function endFieldset(): self
    {
        $this->assertOpen();
        $this->flushFieldset();
        return $this;
    }

    public function addHtml(string $html): self { return $this->append(new RawHtml($html)); }
    public function addText(string $label, array $attributes = []): self { return $this->append($this->input($label, $attributes, 'text')); }
    public function addEmail(string $label, array $attributes = []): self { $attributes += ['placeholder'=>'mail@domain.com','pattern'=>'[A-Za-z0-9._%+-]{3,}@[a-zA-Z]{3,}([.]{1}[a-zA-Z]{2,}|[.]{1}[a-zA-Z]{2,}[.]{1}[a-zA-Z]{2,})']; return $this->append($this->input($label, $attributes, 'email')); }
    public function addPassword(string $label, array $attributes = []): self { $attributes += ['minlength'=>'6','maxlength'=>'64','required'=>'required','placeholder'=>'**********','value'=>'']; return $this->append(new InputElement($label, $attributes, 'password')); }
    public function addPhone(string $label, array $attributes = []): self { return $this->append($this->input($label, $attributes, 'tel')); }
    public function addDate(string $label, array $attributes = []): self { $attributes += ['title'=>'YYYY-dd-MM (e.g. ' . date('Y-m-d') . ')']; return $this->append($this->input($label, $attributes, 'date')); }
    public function addDatetime(string $label, array $attributes = []): self { $attributes += ['placeholder'=>'YYYY-dd-MM H:i:s(e.g. ' . date('Y-m-d H:i:s') . ')']; return $this->append($this->input($label, $attributes, 'datetime')); }
    public function addDatetimeLocal(string $label, array $attributes = []): self { $attributes += ['pattern'=>'[0-9]{4}-[0-9]{2}-[0-9]{2}T[0-9]{2}:[0-9]{2}','placeholder'=>'YYYY-dd-MMTHH:i(e.g. ' . date('Y-m-d\TH:i') . ')']; return $this->append($this->input($label, $attributes, 'datetime-local')); }
    public function addTime(string $label, array $attributes = []): self { return $this->append($this->input($label, $attributes, 'time')); }
    public function addWeek(string $label, array $attributes = []): self { return $this->append($this->input($label, $attributes, 'week')); }
    public function addMonth(string $label, array $attributes = []): self { $attributes += ['pattern'=>'\d{4}-\d{2}','placeholder'=>'YYYY-MM (e.g. ' . date('Y-m') . ')']; return $this->append($this->input($label, $attributes, 'month')); }
    public function addColor(string $label, array $attributes = []): self { $attributes += ['title'=>'6-digit hexidecimal color (e.g. #000000)','pattern'=>'#[a-g0-9]{6}']; return $this->append($this->input($label, $attributes, 'color')); }
    public function addHidden(string $label, array $attributes = []): self { return $this->append($this->input($label, $attributes, 'hidden')); }
    public function addFile(string $label, array $attributes = []): self { $this->multipart(); return $this->append($this->file($label, $attributes)); }
    public function addAudio(string $label, array $attributes = []): self { $this->multipart(); $attributes += ['accept'=>'audio/*']; return $this->append($this->file($label, $attributes)); }
    public function addImage(string $label, array $attributes = []): self { $this->multipart(); $attributes += ['accept'=>'image/*']; return $this->append($this->file($label, $attributes)); }
    public function addVideo(string $label, array $attributes = []): self { $this->multipart(); $attributes += ['accept'=>'video/*']; return $this->append($this->file($label, $attributes)); }
    public function addNumber(string $label, array $attributes = []): self { $attributes += ['min'=>'0','max'=>'100','step'=>'1']; return $this->append($this->input($label, $attributes, 'number')); }
    public function addRange(string $label, array $attributes = []): self { $attributes += ['min'=>'0','max'=>'100','step'=>'1']; return $this->append($this->input($label, $attributes, 'range')); }
    public function addSearch(string $label, array $attributes = []): self { $attributes += ['required'=>'required']; return $this->append($this->input($label, $attributes, 'search')); }
    public function addUrl(string $label, array $attributes = []): self { return $this->append($this->input($label, $attributes, 'url')); }
    public function addButton(string $label, array $attributes = []): self { $attributes += ['value'=>$label]; return $this->append(new InputElement($label, $attributes, 'button')); }
    public function addSubmit(string $label, array $attributes = []): self { $attributes += ['value'=>$label]; return $this->append(new InputElement($label, $attributes, 'submit')); }
    public function addReset(string $label, array $attributes = []): self { $attributes += ['value'=>$label]; return $this->append(new InputElement($label, $attributes, 'reset')); }
    public function addTextarea(string $label, array $attributes = []): self { $attributes['value'] = $this->defaultValue($attributes['name'] ?? $label, $attributes['value'] ?? null); return $this->append(new TextareaElement($label, $attributes)); }
    public function addEditor(string $label, array $attributes = []): self { $attributes['class'] = trim((string)($attributes['class'] ?? '') . ' editor'); return $this->addTextarea($label, $attributes); }
    public function addDatalist(string $label, array $options = [], array $attributes = []): self { $attributes['value'] = $this->defaultValue($attributes['name'] ?? $label, $attributes['value'] ?? null); return $this->append(new DatalistElement($label, $options, $attributes)); }
    public function addCheckbox(string $label, array $options = [], array $attributes = []): self { $attributes['value'] = $this->defaultValue($attributes['name'] ?? $label, $attributes['value'] ?? null); return $this->append(new ChoiceElement($label, $options, $attributes, 'checkbox')); }
    public function addRadio(string $label, array $options = [], array $attributes = []): self { $attributes['value'] = $this->defaultValue($attributes['name'] ?? $label, $attributes['value'] ?? null); return $this->append(new ChoiceElement($label, $options, $attributes, 'radio')); }
    public function addYesNo(string $label, array $attributes = []): self { return $this->addRadio($label, ['1' => 'Yes', '0' => 'No'], $attributes); }
    public function addSelect(string $label, array $options = [], array $attributes = []): self { $attributes['value'] = $this->defaultValue($attributes['name'] ?? $label, $attributes['value'] ?? null); return $this->append(new SelectElement($label, $options, $attributes)); }
    public function addCountries(string $label, array $attributes = []): self { $options = []; foreach (Countries::all() as $country) { $options[$country['code']] = $country['name'] . ((isset($country['native']) && $country['native'] !== '') ? ', (' . $country['native'] . ')' : ''); } $attributes += ['placeholder' => 'Choose a country...']; return $this->addSelect($label, $options, $attributes); }
    public function addCaptcha(string $label = 'Security Code', array $attributes = []): self
    {
        $operators = ['+', 'x', '-'];
        $operator = $operators[random_int(0, 2)];
        switch ($operator) {
            case 'x':
                do { $a = random_int(3, 7); $b = random_int(2, 6); $response = $a * $b; } while ($response > 35);
                break;
            case '-':
                do { $a = random_int(6, 11); $b = random_int(1, 8); $response = $a - $b; } while ($response < 2);
                break;
            default:
                $a = random_int(3, 8); $b = random_int(1, 7); $response = $a + $b;
                break;
        }
        $fullLabel = $label . '. ' . $a . ' ' . $operator . ' ' . $b;
        $attributes += ['name' => $label, 'maxlength'=>'2', 'required'=>'required', 'pattern'=>'[0-9]{1,2}', 'placeholder'=>'Enter result', 'capresponse'=>(string)$response];
        return $this->append($this->input($fullLabel, $attributes, 'text'));
    }

    public function close(): string
    {
        $this->assertOpen();
        $this->flushFieldset();
        $html = $this->form->render();
        $this->form = null;
        return $html;
    }

    private function append(object $element): self
    {
        $this->assertOpen();
        if ($this->fieldset !== null) {
            $this->fieldset->append($element);
        } else {
            $this->form->append($element);
        }
        return $this;
    }

    private function flushFieldset(): void
    {
        if ($this->fieldset !== null) {
            $this->form?->append($this->fieldset);
            $this->fieldset = null;
        }
    }

    private function input(string $label, array $attributes, string $type): InputElement
    {
        if (!in_array($type, ['button', 'submit', 'reset', 'password'], true)) {
            $attributes['value'] = $this->defaultValue($attributes['name'] ?? $label, $attributes['value'] ?? null);
        }
        return new InputElement($label, $attributes, $type);
    }

    private function file(string $label, array $attributes): InputElement
    {
        $attributes['value'] = $this->defaultValue($attributes['name'] ?? $label, $attributes['value'] ?? null);
        return new InputElement($label, $attributes, 'file');
    }

    private function defaultValue(string $name, mixed $default = null): mixed
    {
        return $this->requestDataProvider->value($this->normalize($name), $default);
    }

    private function normalize(string $name): string
    {
        $name = strtolower(trim($name));
        $name = preg_replace('/[^a-z0-9\-_]+/', '-', $name) ?? $name;
        return trim($name, '-');
    }

    private function multipart(): void
    {
        $this->assertOpen();
        $this->form->attributes()->set('enctype', 'multipart/form-data');
    }

    private function assertOpen(): void
    {
        if ($this->form === null) {
            throw new \LogicException('No form is currently open. Call open() first.');
        }
    }
}

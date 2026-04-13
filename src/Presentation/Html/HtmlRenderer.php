<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Presentation\Html;

use Iriven\PhpFormGenerator\Domain\Contract\RendererInterface;
use Iriven\PhpFormGenerator\Domain\Form\FormView;

final class HtmlRenderer implements RendererInterface
{
    public function __construct(
        private readonly Escaper $escaper = new Escaper(),
    ) {
    }

    public function renderForm(FormView $view): string
    {
        $attr = $this->renderAttributes((array) ($view->vars['attr'] ?? []));
        $html = sprintf(
            '<form name="%s" method="%s" action="%s"%s>',
            $this->escaper->escape($view->vars['name'] ?? ''),
            $this->escaper->escape(strtolower((string) ($view->vars['method'] ?? 'post'))),
            $this->escaper->escape($view->vars['action'] ?? ''),
            $attr
        );

        if ($view->errors !== []) {
            $html .= $this->renderErrors($view);
        }

        foreach ($view->children as $child) {
            if (($child->vars['type'] ?? '') === 'hidden') {
                $html .= $this->renderWidget($child);
                continue;
            }
            $html .= $this->renderRow($child);
        }

        $html .= '</form>';

        return $html;
    }

    public function renderRow(FormView $view): string
    {
        $html = '<div class="form-row">';
        $html .= $this->renderLabel($view);
        $html .= $this->renderWidget($view);
        $html .= $this->renderErrors($view);

        if (($view->vars['help'] ?? null) !== null) {
            $html .= sprintf('<small>%s</small>', $this->escaper->escape($view->vars['help']));
        }

        $html .= '</div>';

        return $html;
    }

    public function renderWidget(FormView $view): string
    {
        $type = (string) ($view->vars['type'] ?? 'text');
        $name = (string) ($view->vars['full_name'] ?? $view->vars['name'] ?? '');
        $id = (string) ($view->vars['id'] ?? '');
        $value = $view->vars['value'] ?? null;
        $attr = (array) ($view->vars['attr'] ?? []);
        if (($view->vars['required'] ?? false) === true) {
            $attr['required'] = 'required';
        }

        if ($type === 'textarea' || $type === 'editor') {
            return sprintf(
                '<textarea name="%s" id="%s"%s>%s</textarea>',
                $this->escaper->escape($name),
                $this->escaper->escape($id),
                $this->renderAttributes($attr),
                $this->escaper->escape((string) $value)
            );
        }

        if ($type === 'choice' || $type === 'country' || $type === 'yes_no') {
            $multiple = (bool) ($view->vars['multiple'] ?? false);
            $choices = (array) ($view->vars['choices'] ?? []);
            $current = $multiple ? (array) $value : [(string) $value];
            $html = sprintf(
                '<select name="%s%s" id="%s"%s%s>',
                $this->escaper->escape($name),
                $multiple ? '[]' : '',
                $this->escaper->escape($id),
                $this->renderAttributes($attr),
                $multiple ? ' multiple' : ''
            );
            foreach ($choices as $label => $choiceValue) {
                $selected = in_array((string) $choiceValue, array_map('strval', $current), true) ? ' selected' : '';
                $html .= sprintf(
                    '<option value="%s"%s>%s</option>',
                    $this->escaper->escape($choiceValue),
                    $selected,
                    $this->escaper->escape($label)
                );
            }
            $html .= '</select>';
            return $html;
        }

        if ($type === 'datalist') {
            $listId = $id . '_list';
            $html = sprintf(
                '<input type="text" name="%s" id="%s" value="%s" list="%s"%s>',
                $this->escaper->escape($name),
                $this->escaper->escape($id),
                $this->escaper->escape((string) $value),
                $this->escaper->escape($listId),
                $this->renderAttributes($attr),
            );
            $html .= sprintf('<datalist id="%s">', $this->escaper->escape($listId));
            foreach ((array) ($view->vars['choices'] ?? []) as $label => $choiceValue) {
                $html .= sprintf('<option value="%s">%s</option>', $this->escaper->escape($choiceValue), $this->escaper->escape($label));
            }
            $html .= '</datalist>';
            return $html;
        }

        if ($type === 'checkbox') {
            $checked = $value ? ' checked' : '';
            return sprintf(
                '<input type="checkbox" name="%s" id="%s" value="1"%s%s>',
                $this->escaper->escape($name),
                $this->escaper->escape($id),
                $checked,
                $this->renderAttributes($attr),
            );
        }

        if ($type === 'html') {
            return ($view->vars['safe_html'] ?? false) ? (string) $value : $this->escaper->escape((string) $value);
        }

        if ($type === 'audio' || $type === 'video' || $type === 'image') {
            $src = $this->escaper->escape((string) $value);
            return match ($type) {
                'audio' => sprintf('<audio controls src="%s"></audio>', $src),
                'video' => sprintf('<video controls src="%s"></video>', $src),
                default => sprintf('<img src="%s" alt="">', $src),
            };
        }

        if ($type === 'collection') {
            $html = '<div class="collection">';
            foreach ((array) $value as $entry) {
                $html .= sprintf('<div class="collection-item"><input type="text" name="%s[]" value="%s"></div>', $this->escaper->escape($name), $this->escaper->escape((string) $entry));
            }
            $html .= '</div>';
            return $html;
        }

        if ($type === 'button' || $type === 'submit' || $type === 'reset') {
            return sprintf(
                '<button type="%s" name="%s" id="%s"%s>%s</button>',
                $this->escaper->escape($type),
                $this->escaper->escape($name),
                $this->escaper->escape($id),
                $this->renderAttributes($attr),
                $this->escaper->escape((string) (($view->vars['label'] ?? $value ?? ucfirst($type))))
            );
        }

        $inputType = in_array($type, ['text','email','password','number','radio','file','date','time','datetime-local','hidden','url','search','range','color','month','week','tel'], true)
            ? $type
            : 'text';

        return sprintf(
            '<input type="%s" name="%s" id="%s" value="%s"%s>',
            $this->escaper->escape($inputType),
            $this->escaper->escape($name),
            $this->escaper->escape($id),
            $this->escaper->escape((string) $value),
            $this->renderAttributes($attr),
        );
    }

    public function renderLabel(FormView $view): string
    {
        $label = $view->vars['label'] ?? null;
        if ($label === null || ($view->vars['type'] ?? '') === 'hidden' || ($view->vars['type'] ?? '') === 'html') {
            return '';
        }

        return sprintf(
            '<label for="%s">%s</label>',
            $this->escaper->escape((string) ($view->vars['id'] ?? '')),
            $this->escaper->escape((string) $label),
        );
    }

    public function renderErrors(FormView $view): string
    {
        if ($view->errors === []) {
            return '';
        }

        $html = '<ul class="form-errors">';
        foreach ($view->errors as $error) {
            $html .= sprintf('<li>%s</li>', $this->escaper->escape($error));
        }
        $html .= '</ul>';

        return $html;
    }

    private function renderAttributes(array $attr): string
    {
        $parts = [];
        foreach ($attr as $key => $value) {
            if ($value === false || $value === null) {
                continue;
            }
            if ($value === true) {
                $parts[] = sprintf(' %s', $this->escaper->escape((string) $key));
                continue;
            }
            $parts[] = sprintf(' %s="%s"', $this->escaper->escape((string) $key), $this->escaper->escape((string) $value));
        }
        return implode('', $parts);
    }
}

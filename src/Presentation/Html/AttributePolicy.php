<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Presentation\Html;

final class AttributePolicy
{
    private const COMMON = ['accesskey','class','contenteditable','contextmenu','dir','draggable','dropzone','hidden','id','lang','spellcheck','style','tabindex','title','translate','aria-label','aria-describedby','role'];
    private const EVENTS = ['onblur','onchange','oncontextmenu','onfocus','oninput','oninvalid','onreset','onsearch','onselect','onsubmit','onkeydown','onkeypress','onkeyup','onclick','ondblclick','ondrag','ondragend','ondragenter','ondragleave','ondragover','ondragstart','ondrop','onmousedown','onmousemove','onmouseout','onmouseover','onmouseup','onmousewheel','onscroll','onwheel'];
    private const BUTTON = ['autofocus','disabled','form','formaction','formenctype','formmethod','formnovalidate','formtarget','name','type','value'];
    private const CHECKBOX = ['autofocus','checked','disabled','form','indeterminate','name','required','type','value'];
    private const COLOR = ['autocomplete','autofocus','disabled','form','list','name','type','value'];
    private const DATE = ['autocomplete','autofocus','disabled','form','list','max','min','name','readonly','required','step','type','value','placeholder'];
    private const FIELDSET = ['disabled','form','name'];
    private const FILE = ['accept','autofocus','disabled','form','multiple','name','required','type','value','data-max-size'];
    private const FORM = ['accept','accept-charset','action','autocomplete','enctype','method','name','novalidate','target'];
    private const HIDDEN = ['form','name','type','value'];
    private const IMAGE = ['alt','autofocus','disabled','form','formaction','formenctype','formmethod','formnovalidate','formtarget','height','name','src','type','value','width'];
    private const LABEL = ['for','form','class','style'];
    private const OPTGROUP = ['disabled','label'];
    private const OPTION = ['disabled','label','value','selected'];
    private const RANGE = ['autocomplete','autofocus','disabled','form','list','max','min','name','step','type','value'];
    private const RESET = ['autofocus','disabled','form','name','type','value'];
    private const SELECT = ['autofocus','disabled','form','multiple','name','required','size','value'];
    private const TEXT = ['autocomplete','autofocus','disabled','form','list','minlength','maxlength','name','pattern','placeholder','readonly','required','size','type','value'];
    private const TEXTAREA = ['autofocus','cols','dirname','disabled','form','maxlength','name','placeholder','readonly','required','rows','wrap'];

    public static function allows(string $type, string $attribute): bool
    {
        $attribute = Str::normalizeKey($attribute);
        if (str_starts_with($attribute, 'data-')) {
            return true;
        }

        $accepted = array_merge(self::COMMON, self::EVENTS);

        switch ($type) {
            case 'date':
            case 'datetime':
            case 'datetime-local':
            case 'month':
            case 'number':
            case 'time':
            case 'week':
                $accepted = array_merge($accepted, self::DATE);
                break;
            case 'color':
                $accepted = array_merge($accepted, self::COLOR);
                break;
            case 'file':
                $accepted = array_merge($accepted, self::FILE);
                break;
            case 'image':
            case 'video':
                $accepted = array_merge($accepted, self::IMAGE);
                break;
            case 'radio':
            case 'checkbox':
                $accepted = array_merge($accepted, self::CHECKBOX);
                if ($type === 'radio') {
                    $accepted = array_diff($accepted, ['indeterminate']);
                }
                break;
            case 'submit':
            case 'button':
                $accepted = array_merge($accepted, self::BUTTON);
                break;
            case 'reset':
                $accepted = array_merge($accepted, self::RESET);
                break;
            case 'range':
                $accepted = array_merge($accepted, self::RANGE);
                break;
            case 'textarea':
                $accepted = array_merge($accepted, self::TEXTAREA);
                break;
            case 'optgroup':
                $accepted = array_merge($accepted, self::OPTGROUP);
                break;
            case 'option':
                $accepted = array_merge($accepted, self::OPTION);
                break;
            case 'label':
                $accepted = array_merge($accepted, self::LABEL);
                break;
            case 'select':
                $accepted = array_merge($accepted, self::SELECT);
                break;
            case 'form':
                $accepted = array_merge($accepted, self::FORM);
                break;
            case 'fieldset':
                $accepted = array_merge($accepted, self::FIELDSET);
                break;
            case 'hidden':
                $accepted = array_merge($accepted, self::HIDDEN);
                break;
            default:
                if ($type === 'email') {
                    $accepted[] = 'multiple';
                }
                $accepted = array_merge($accepted, self::TEXT);
                break;
        }

        return in_array($attribute, array_unique($accepted), true);
    }
}

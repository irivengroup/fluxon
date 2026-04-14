<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Event;

final class FormEvents
{
    public const PRE_SET_DATA = 'form.pre_set_data';
    public const PRE_SUBMIT = 'form.pre_submit';
    public const SUBMIT = 'form.submit';
    public const POST_SUBMIT = 'form.post_submit';
    public const VALIDATION_ERROR = 'form.validation_error';
}

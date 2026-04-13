<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Domain\Event;

final class FormEvents
{
    public const PRE_SET_DATA = 'pre_set_data';
    public const PRE_SUBMIT = 'pre_submit';
    public const SUBMIT = 'submit';
    public const POST_SUBMIT = 'post_submit';
    public const VALIDATION_ERROR = 'validation_error';
}

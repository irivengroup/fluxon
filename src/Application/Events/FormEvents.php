<?php
declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Application\Events;

/** @api */
final class FormEvents
{
    public const PRE_BUILD = 'form.pre_build';
    public const POST_BUILD = 'form.post_build';
    public const PRE_RENDER = 'form.pre_render';
    public const POST_RENDER = 'form.post_render';
    public const PRE_SUBMIT = 'form.pre_submit';
    public const POST_SUBMIT = 'form.post_submit';
}

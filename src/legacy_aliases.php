<?php

declare(strict_types=1);

use Iriven\PhpFormGenerator\Domain\Field\DateTimeType;
use Iriven\PhpFormGenerator\Domain\Field\TextareaType;

if (!class_exists(DateTimeType::class, false)) {
    require_once __DIR__ . '/Domain/Field/DateTimeType.php';
}

if (!class_exists(TextareaType::class, false)) {
    require_once __DIR__ . '/Domain/Field/TextareaType.php';
}

if (!class_exists('Iriven\\PhpFormGenerator\\Domain\\Field\\DatetimeType', false)) {
    class_alias(DateTimeType::class, 'Iriven\\PhpFormGenerator\\Domain\\Field\\DatetimeType');
}

if (!class_exists('Iriven\\PhpFormGenerator\\Domain\\Field\\TextAreaType', false)) {
    class_alias(TextareaType::class, 'Iriven\\PhpFormGenerator\\Domain\\Field\\TextAreaType');
}

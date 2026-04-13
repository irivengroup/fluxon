<?php

declare(strict_types=1);

namespace Iriven\PhpFormGenerator\Presentation\Html;

final class Str
{
    public static function normalizeKey(string $value): string
    {
        $value = trim($value);
        $value = preg_replace('/([a-z])([A-Z])/', '$1-$2', $value) ?? $value;
        $value = preg_replace('/[^a-zA-Z0-9\-\_\[\]]+/', '-', $value) ?? $value;
        $value = strtolower(trim($value, '-'));

        return str_replace('_', '-', $value);
    }

    public static function normalizeName(string $value): string
    {
        $value = strip_tags($value);
        $value = preg_replace('/[^a-zA-Z0-9]+/', '-', $value) ?? $value;
        $value = strtolower(trim($value, '-'));

        return $value !== '' ? $value : 'field';
    }

    public static function elementIdFromName(string $name): string
    {
        $base = preg_replace('/[^a-zA-Z0-9]+/', '-', $name) ?? $name;
        $base = trim($base, '-');

        return 'input-' . strtolower($base !== '' ? $base : 'field');
    }
}

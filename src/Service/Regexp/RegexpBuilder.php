<?php
namespace App\Service\Regexp;

class RegexpBuilder
{
    public function buildRegex(array $elements): string
    {
        $regexParts = [];
        $elements = array_values($elements);

        foreach ($elements as $idx => $element) {
            $type = $element['type'];

            if ($type === 'seq') {
                $nextEl = $elements[$idx + 1] ?? null;
                $regex = $this->elementToRegex($element);

                if ($nextEl) {
                    $regex .= '(?=' . $this->elementToRegex($nextEl) . ')';
                }

                $regexParts[] = $regex;
            } else {
                $regexParts[] = $this->elementToRegex($element);
            }
        }

        return '/^' . implode('', $regexParts) . '$/';
    }

    private function elementToRegex(array $el): string
    {
        $type = $el['type'];
        $val = $el['value'] ?? '';

        return match ($type) {
            'seq' => '\d+' . preg_quote($val, '/'),
            'guid' => $this->guidFormatToRegex($val),
            'date' => $this->dateFormatToRegex($val),
            'rand20', 'rand32', 'rand6', 'rand9' => $this->randFormatToRegex($val),
            'fixed' => preg_quote($val, '/'),
            default => '.+',
        };
    }

    private function guidFormatToRegex(string $format): string
    {
        $suffix = $format ? '[-_]?' : '';

        return '[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}' . $suffix;
    }

    private function dateFormatToRegex(string $format): string
    {
        $map = [
            'yyyy' => '\d{4}',
            'ddd' => '[A-Z]{3}',
            'mm' => '\d{2}',
            'dd' => '\d{2}',
        ];

        return preg_replace_callback('/yyyy|ddd|mm|dd|[-_]/', function ($m) use ($map) {
            return $map[$m[0]] ?? preg_quote($m[0], '/');
        }, $format);
    }

    private function randFormatToRegex(string $val): string
    {
        if (preg_match('/^D(\d+)([-_]?)$/', $val, $m)) {
            return '\d{' . $m[1] . '}' . preg_quote($m[2], '/');
        }

        if (preg_match('/^X(\d+)([-_]?)$/', $val, $m)) {
            return '[0-9A-F]{' . $m[1] . '}' . preg_quote($m[2], '/');
        }

        if (preg_match('/^B(\d+)([-_]?)$/', $val, $m)) {
            return '[01]{' . $m[1] . '}' . preg_quote($m[2], '/');
        }

        if (preg_match('/^O(\d+)([-_]?)$/', $val, $m)) {
            return '[0-7]{' . $m[1] . '}' . preg_quote($m[2], '/');
        }

        return '.+';
    }

}

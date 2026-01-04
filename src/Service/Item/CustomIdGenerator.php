<?php
namespace App\Service\Item;

use App\Exception\CustomIdGeneratorException;
use App\Repository\ItemRepository;
use Symfony\Component\Uid\Uuid;

class CustomIdGenerator
{
    public function __construct(private ItemRepository $itemRepository)
    {}

    public function generate(array $elements): string
    {
        $format = $elements;
        $parts  = [];

        foreach ($format as $element) {
            $parts[] = $this->renderElement($element);
        }

        return implode('', $parts);
    }

    private function renderElement(array $element): string
    {
        return match ($element['type']) {
            'fixed'  => $element['value'],
            'rand20' => $this->parseValue($element['value'], random_int(0, (2 ** 20) - 1)),
            'rand32' => $this->parseValue($element['value'], random_int(0, (2 ** 32) - 1)),
            'rand6'  => $this->parseValue($element['value'], random_int(0, 999999)),
            'rand9'  => $this->parseValue($element['value'], random_int(0, 999999999)),
            'guid'   => Uuid::v4()->toRfc4122() . $element['value'],
            'date'   => $this->parseDate($element['value']),
            'seq'    => (string) $this->itemRepository->getMaxSequence() . $element['value'],
            default  => throw new CustomIdGeneratorException("Unknown element type"),
        };
    }

    private function parseDate(string $format)
    {
        $date = new \DateTime();

        $formattedDate = str_replace([
            'yyyy',
            'ddd',
            'dd',
            'mm',
        ], [
            $date->format('Y'),
            substr(strtoupper($date->format('l')), 0, 3),
            $date->format('d'),
            $date->format('m'),
        ], $format);

        return $formattedDate;
    }

    private function parseValue(?string $value, int $rawNumber): string
    {
        if (preg_match('/^X(\d+)([-_]?)$/', $value, $m)) {
            $len = $m[1];
            $hex = strtoupper(dechex($rawNumber));

            return $this->formatFixedLength($hex, $len) . $m[2];
        }

        if (preg_match('/^D(\d+)([-_]?)$/', $value, $m)) {
            $len = $m[1];
            return $this->formatFixedLength($rawNumber, $len) . $m[2];
        }

        if (preg_match('/^B(\d+)([-_]?)$/', $value, $m)) {
            $len = $m[1];
            $bin = base_convert((string) $rawNumber, 10, 2);
            return $this->formatFixedLength($bin, $len) . $m[2];
        }

        if (preg_match('/^O(\d+)([-_]?)$/', $value, $m)) {
            $len = $m[1];
            $oct = base_convert((string) $rawNumber, 10, 8);
            return $this->formatFixedLength($oct, $len) . $m[2];
        }

        throw new CustomIdGeneratorException("Unknown value format: $value");
    }

    private function formatFixedLength(string $input, int $len): string
    {
        $padded = str_pad($input, $len, '0', STR_PAD_LEFT);
        return substr($padded, -$len);
    }
}

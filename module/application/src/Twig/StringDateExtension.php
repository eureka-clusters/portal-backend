<?php

declare(strict_types=1);

namespace Application\Twig;

use DateTime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

use function ceil;
use function date;
use function floor;
use function mktime;

final class StringDateExtension extends AbstractExtension
{
    private const FILTER_NAME = 'string_date';

    public function getFilters(): array
    {
        return [
            new TwigFilter(
                name: self::FILTER_NAME,
                callable: $this->processFilter(...)
            ),
        ];
    }

    public function processFilter(?DateTime $date, string $format = 'd M Y'): ?string
    {
        if (null === $date) {
            return null;
        }

        $datetime       = $date->getTimestamp();
        $lastWeek       = mktime(
            hour: 0,
            minute: 0,
            second: 0,
            month: (int) date(format: 'm'),
            day: (int) date(
                format: 'd') - 6,
            year: (int) date(format: 'Y'));
        $yesterdayStart = mktime(
            hour: 0,
            minute: 0,
            second: 0,
            month: (int) date(format: 'm'),
            day: (int) date(
                format: 'd') - 1,
            year: (int) date(format: 'Y'));
        $todayStart     = mktime(
            hour: 0,
            minute: 0,
            second: 0,
            month: (int) date(format: 'm'),
            day: (int) date(
            format: 'd'),
            year: (int) date(format: 'Y'));
        $todayEnd       = mktime(
            hour: 0,
            minute: 0,
            second: 0,
            month: (int) date(format: 'm'),
            day: (int) date(
                format: 'd') + 1,
            year: (int) date(format: 'Y'));
        $tomorrowEnd    = mktime(
            hour: 0,
            minute: 0,
            second: 0,
            month: (int) date(format: 'm'),
            day: (int) date(
                format: 'd') + 2,
            year: (int) date(format: 'Y'));
        $nextWeek       = mktime(
            hour: 0,
            minute: 0,
            second: 0,
            month: (int) date(format: 'm'),
            day: (int) date(
                format: 'd') + 7,
            year: (int) date(format: 'Y'));
        $hasTime        = date(format: 'Hi', timestamp: $datetime) !== '0000';

        if ($datetime < $lastWeek) {
            //IT LONGER THEN A WEEK AGO
            $out = date(format: $format, timestamp: $datetime);
        } elseif ($datetime < $yesterdayStart) {
            // LESS THEN A WEEK AGO
            $out = floor(num: ($todayStart - $datetime) / (3600 * 24)) . ' days ago';
        } elseif ($datetime < $todayStart) {
            // YESTERDAY
            $out = 'Yesterday' . ($hasTime ? ' ' . date(format: 'H\hi', timestamp: $datetime) : '');
        } elseif ($datetime < $todayEnd) {
            // TODAY
            $out = 'Today' . ($hasTime ? ' ' . date(format: 'H:i', timestamp: $datetime) : '');
        } elseif ($datetime < $tomorrowEnd) {
            // TOMORROW
            $out = 'Tomorrow ' . ($hasTime ? ' ' . date(format: 'H:i', timestamp: $datetime) : '');
        } elseif ($datetime < $nextWeek) {
            // NEXT WEEK
            $out = 'In ' . ceil(num: ($datetime - $todayEnd) / (3600 * 24)) . ' days';
        } else {
            // FURTHER AWAY
            $out = date(format: $format, timestamp: $datetime);
        }

        return $out;
    }

    public function getName(): string
    {
        return self::FILTER_NAME;
    }
}

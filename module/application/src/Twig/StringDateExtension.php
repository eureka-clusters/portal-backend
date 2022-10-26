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
                self::FILTER_NAME,
                $this->processFilter(...)
            ),
        ];
    }

    public function processFilter(?DateTime $date, string $format = 'd M Y'): ?string
    {
        if (null === $date) {
            return null;
        }

        $datetime       = $date->getTimestamp();
        $lastWeek       = mktime(0, 0, 0, (int) date('m'), (int) date('d') - 6, (int) date('Y'));
        $yesterdayStart = mktime(0, 0, 0, (int) date('m'), (int) date('d') - 1, (int) date('Y'));
        $todayStart     = mktime(0, 0, 0, (int) date('m'), (int) date('d'), (int) date('Y'));
        $todayEnd       = mktime(0, 0, 0, (int) date('m'), (int) date('d') + 1, (int) date('Y'));
        $tomorrowEnd    = mktime(0, 0, 0, (int) date('m'), (int) date('d') + 2, (int) date('Y'));
        $nextWeek       = mktime(0, 0, 0, (int) date('m'), (int) date('d') + 7, (int) date('Y'));
        $hasTime        = date('Hi', $datetime) !== '0000';

        if ($datetime < $lastWeek) {
            //IT LONGER THEN A WEEK AGO
            $out = date($format, $datetime);
        } elseif ($datetime < $yesterdayStart) {
            // LESS THEN A WEEK AGO
            $out = floor(($todayStart - $datetime) / (3600 * 24)) . ' days ago';
        } elseif ($datetime < $todayStart) {
            // YESTERDAY
            $out = 'Yesterday' . ($hasTime ? ' ' . date('H\hi', $datetime) : '');
        } elseif ($datetime < $todayEnd) {
            // TODAY
            $out = 'Today' . ($hasTime ? ' ' . date('H:i', $datetime) : '');
        } elseif ($datetime < $tomorrowEnd) {
            // TOMORROW
            $out = 'Tomorrow ' . ($hasTime ? ' ' . date('H:i', $datetime) : '');
        } elseif ($datetime < $nextWeek) {
            // NEXT WEEK
            $out = 'In ' . ceil(($datetime - $todayEnd) / (3600 * 24)) . ' days';
        } else {
            // FURTHER AWAY
            $out = date($format, $datetime);
        }

        return $out;
    }

    public function getName(): string
    {
        return self::FILTER_NAME;
    }
}

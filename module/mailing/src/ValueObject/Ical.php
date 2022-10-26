<?php

declare(strict_types=1);

namespace Mailing\ValueObject;

use Admin\Entity\User;
use DateTime;
use Eluceo\iCal\Domain\Entity\Calendar;
use Eluceo\iCal\Domain\Entity\Event;
use Eluceo\iCal\Domain\ValueObject\EmailAddress;
use Eluceo\iCal\Domain\ValueObject\Location;
use Eluceo\iCal\Domain\ValueObject\Organizer;
use Eluceo\iCal\Domain\ValueObject\TimeSpan;
use Eluceo\iCal\Presentation\Factory\CalendarFactory;
use JetBrains\PhpStorm\ArrayShape;
use Laminas\Mime\Mime;
use Laminas\Mime\Part;

use function base64_encode;

final class Ical
{
    public function __construct(
        private readonly DateTime $startDate,
        private readonly DateTime $endDate,
        private readonly string $title,
        private readonly string $summary,
        private readonly ?string $location,
        private readonly User $organiser
    ) {
    }

    public function toMimePart(): Part
    {
        $part = new Part(content: $this->getCalendar());
        $part->setType(type: 'text/calendar');
        $part->setDisposition(disposition: Mime::DISPOSITION_ATTACHMENT);
        $part->setEncoding(encoding: Mime::ENCODING_BASE64);
        $part->setFileName(fileName: 'meeting.ics');

        return $part;
    }

    private function getCalendar(): string
    {
        $event = new Event();

        if (null !== $this->location) {
            $event->setLocation(new Location($this->location));
        }

        $event->setOccurrence(
            new TimeSpan(
                new \Eluceo\iCal\Domain\ValueObject\DateTime($this->startDate, false),
                new \Eluceo\iCal\Domain\ValueObject\DateTime($this->endDate, false)
            )
        );

        $event->setOrganizer(
            new Organizer(
                new EmailAddress($this->organiser->getEmail()),
                $this->organiser->parseFullName(),
                null,
                new EmailAddress('solodb@imec.be'),
            )
        );

        $event->setSummary($this->summary);
        $event->setDescription($this->title);

        $calendar = new Calendar([$event]);

        $componentFactory  = new CalendarFactory();
        $calendarComponent = $componentFactory->createCalendar($calendar);

        return (string) $calendarComponent;
    }

    #[ArrayShape(shape: [
        'ContentType'   => "string",
        'Filename'      => "string",
        'Base64Content' => "string",
    ])] public function toArray(): array
    {
        return [
            'ContentType'   => 'text/calendar',
            'Filename'      => 'meeting.ics',
            'Base64Content' => base64_encode(string: $this->getCalendar()),
        ];
    }
}

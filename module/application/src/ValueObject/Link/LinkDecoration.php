<?php

declare(strict_types=1);

namespace Application\ValueObject\Link;

use function implode;
use function sprintf;
use function str_replace;

final class LinkDecoration
{
    public const SHOW_TEXT          = 'text';
    public const SHOW_ICON          = 'icon';
    public const SHOW_ICON_AND_TEXT = 'icon-and-text';
    public const SHOW_BUTTON        = 'button';
    public const SHOW_DANGER_BUTTON = 'danger-button';
    public const SHOW_RAW           = 'raw';

    private const ACTION_NEW    = 'new';
    private const ACTION_EDIT   = 'edit';
    private const ACTION_DELETE = 'delete';

    private static string $iconTemplate = '<i class="fa %s fa-fw"></i>';

    private static string $linkTemplate = '<a href="%%s"%s%s>%s</a>';

    private static array $defaultIcons = [
        self::ACTION_NEW    => 'fa-plus',
        self::ACTION_EDIT   => 'fa-pencil-square-o',
        self::ACTION_DELETE => 'fa-trash',
    ];

    private ?string $icon;

    public function __construct(
        private readonly string $show = self::SHOW_TEXT,
        private readonly LinkText $linkText = new LinkText(),
        ?string $action = null,
        ?string $icon = null
    ) {
        $this->icon = $icon ?? self::$defaultIcons[(string) $action] ?? null;
    }

    public static function fromArray(array $params): LinkDecoration
    {
        return new self(
            show: $params['show'] ?? self::SHOW_TEXT,
            linkText: LinkText::fromArray(params: $params),
            action: $params['action'] ?? null,
            icon: $params['icon'] ?? null
        );
    }

    public function parse(): string
    {
        if ($this->show === self::SHOW_RAW) {
            return '%s';
        }

        $content = [];
        $classes = [];
        switch ($this->show) {
            case self::SHOW_ICON:
                if ($this->icon !== null) {
                    $content[] = sprintf(self::$iconTemplate, $this->icon);
                }
                break;
            case self::SHOW_ICON_AND_TEXT:
            case self::SHOW_BUTTON:
            case self::SHOW_DANGER_BUTTON:
                if ($this->icon !== null) {
                    $content[] = sprintf(self::$iconTemplate, $this->icon);
                }
                $text = $this->linkText->parse();
                if (! empty($text)) {
                    $content[] = sprintf(' %s', $text);
                }
                if ($this->show === self::SHOW_BUTTON) {
                    $classes = ['btn', 'btn-primary'];
                }
                if ($this->show === self::SHOW_DANGER_BUTTON) {
                    $classes = ['btn', 'btn-danger'];
                }
                break;
            case self::SHOW_TEXT:
            default:
                $content[] = $this->linkText->parse();
                break;
        }

        return sprintf(
            self::$linkTemplate,
            empty($this->linkText->getTitle()) ? '' : sprintf(
                ' title="%s"',
                str_replace(search: '%', replace: '&#37;', subject: $this->linkText->getTitle())
            ),
            empty($classes) ? '' : sprintf(' class="%s"', implode(separator: ' ', array: $classes)),
            str_replace(search: '%', replace: '&#37;', subject: implode(separator: $content))
        );
    }
}

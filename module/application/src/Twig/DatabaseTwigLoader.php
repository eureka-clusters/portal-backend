<?php

declare(strict_types=1);

namespace Application\Twig;

use Admin\Entity\Template;
use DateTime;
use Doctrine\ORM\EntityManager;
use Twig\Error\LoaderError;
use Twig\Loader\LoaderInterface;
use Twig\Source;

use function sprintf;

class DatabaseTwigLoader implements LoaderInterface
{
    public function __construct(private readonly EntityManager $entityManager)
    {
    }

    public function getSourceContext($name): Source
    {
        /** @var Template|\Mailing\Entity\Template $template */
        $template = $template = $this->getTemplate($name);

        if (null === $template) {
            throw new LoaderError(sprintf('Template "%s" does not exist.', $name));
        }

        if ($template instanceof TemplateInterface) {
            throw new LoaderError(sprintf('Template "%s" should implement %s', $name, TemplateInterface::class));
        }

        return new Source($template->parseSourceContent(), $template->parseName());
    }

    protected function getTemplate(string $template): ?Template
    {
        /** @var Template $template */
        $template = $this->entityManager->getRepository(Template::class)->findOneBy(
            [
                'template' => $template,
            ]
        );

        return $template;
    }

    public function exists($template): bool
    {
        return null !== $this->getTemplate($template);
    }

    public function getCacheKey($name): string
    {
        return $name;
    }

    public function isFresh($name, $time): bool
    {
        $template = $this->getTemplate($name);

        if (null === $template) {
            return false;
        }

        $lastModified = $template->getLastUpdate();

        if (null === $lastModified) {
            return false;
        }
        $date = new DateTime();
        $date->setTimestamp($time);

        return $lastModified <= $date;
    }
}

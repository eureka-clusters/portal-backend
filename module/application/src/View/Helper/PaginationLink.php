<?php

declare(strict_types=1);

namespace Application\View\Helper;

use Laminas\Http\Request;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\Router\Http\RouteMatch;
use Laminas\View\Helper\Url;
use Psr\Container\ContainerInterface;

use function array_merge;
use function sprintf;

final class PaginationLink
{
    private RouteMatch $routeMatch;

    private Url $url;

    private Request $request;

    private TranslatorInterface $translator;

    public function __construct(ContainerInterface $container)
    {
        $this->routeMatch = $container->get('application')->getMvcEvent()->getRouteMatch();
        $this->url        = $container->get('ViewHelperManager')->get(Url::class);
        $this->request    = $container->get('Request');
        $this->translator = $container->get(TranslatorInterface::class);
    }

    public function __invoke($page, $show): string
    {
        $params = array_merge(
            $this->routeMatch->getParams(),
            [
                'page' => $page,
            ]
        );

        $uri = '<a href="%s" class="page-link" title="%s">%s</a>';

        return sprintf(
            $uri,
            $this->url->__invoke(
                name: $this->routeMatch->getMatchedRouteName(),
                params: $params,
                options: ['query' => $this->request->getQuery()->toArray()]
            ),
            sprintf($this->translator->translate(message: 'txt-go-to-page-%s'), $show),
            $show
        );
    }
}

<?php

declare(strict_types=1);

namespace Application\Controller\Plugin;

use Application\ValueObject\SearchFormResult;
use Doctrine\Common\Collections\Criteria;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;
use Laminas\Http\Request;
use Laminas\Mvc\Application;
use Laminas\Mvc\Controller\Plugin\AbstractPlugin;
use Psr\Container\ContainerInterface;

use function urldecode;
use function urlencode;

final class GetFilter extends AbstractPlugin
{
    private SearchFormResult $filter;

    public function __construct(private readonly ContainerInterface $container)
    {
    }

    public function __invoke(): GetFilter
    {
        /** @var Application $application */
        $application = $this->container->get('application');
        $encodedFilter = urldecode((string)$application->getMvcEvent()->getRouteMatch()->getParam('encodedFilter'));
        /** @var Request $request */
        $request = $application->getMvcEvent()->getRequest();

        //Initiate the filter
        $this->filter = new SearchFormResult(
            order: $request->getQuery('order', 'default'),
            direction: $request->getQuery('direction', Criteria::ASC)
        );

        if (!empty($encodedFilter)) {
            $this->filter->updateFromEncodedFilter($encodedFilter);
        }

        // If the form is submitted, refresh the URL
        if ($request->getQuery('query') !== null) {
            $this->filter->setQuery($request->getQuery('query'));
            $this->filter->setFilter($request->getQuery('filter', []));
        }

        if (null !== $request->getQuery('order')) {
            $this->filter->setOrder($request->getQuery('order'));
        }

        if (null !== $request->getQuery('direction')) {
            $this->filter->setDirection($request->getQuery('direction'));
        }

        // If the form is submitted, refresh the URL
        if ($request->getQuery('reset') !== null) {
            $this->filter = new SearchFormResult(
                order: $request->getQuery('order', 'default'),
                direction: $request->getQuery('direction', Criteria::ASC)
            );
        }

        return $this;
    }

    public function getFilter(): SearchFormResult
    {
        return $this->filter;
    }

    #[Pure] public function getOrder(): string
    {
        return $this->filter->getOrder();
    }

    #[Pure] public function getDirection(): string
    {
        return $this->filter->getDirection();
    }

    public function getEncodedFilter(): ?string
    {
        return urlencode($this->filter->getHash());
    }

    #[Pure] #[ArrayShape([
        'filter' => "array",
        'query' => "null|string"
    ])] public function getFilterFormData(): array
    {
        return [
            'filter' => $this->filter->getFilter(),
            'query' => $this->filter->getQuery(),
        ];
    }
}

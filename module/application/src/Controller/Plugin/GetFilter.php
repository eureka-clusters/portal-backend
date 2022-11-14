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
        $encodedFilter = urldecode(
            string: (string)$application->getMvcEvent()->getRouteMatch()->getParam(
                name: 'encodedFilter'
            )
        );
        /** @var Request $request */
        $request = $application->getMvcEvent()->getRequest();

        //Initiate the filter
        $this->filter = new SearchFormResult(
            order: $request->getQuery(name: 'order', default: 'default'),
            direction: $request->getQuery(name: 'direction', default: Criteria::ASC)
        );

        if (!empty($encodedFilter)) {
            $this->filter->updateFromEncodedFilter(encodedFilter: $encodedFilter);
        }

        // If the form is submitted, refresh the URL
        if ($request->getQuery(name: 'query') !== null) {
            $this->filter->setQuery(query: $request->getQuery(name: 'query'));
            $this->filter->setFilter(filter: $request->getQuery(name: 'filter', default: []));
        }

        if (null !== $request->getQuery(name: 'order')) {
            $this->filter->setOrder(order: $request->getQuery(name: 'order'));
        }

        if (null !== $request->getQuery(name: 'direction')) {
            $this->filter->setDirection(direction: $request->getQuery(name: 'direction'));
        }

        // If the form is submitted, refresh the URL
        if ($request->getQuery(name: 'reset') !== null) {
            $this->filter = new SearchFormResult(
                order: $request->getQuery(name: 'order', default: 'default'),
                direction: $request->getQuery(name: 'direction', default: Criteria::ASC)
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
        return urlencode(string: $this->filter->getHash());
    }

    #[Pure] #[ArrayShape(shape: [
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

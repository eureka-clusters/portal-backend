<?php

declare(strict_types=1);

namespace Deeplink\Service;

use Admin\Entity\User;
use Application\Service\AbstractService;
use Application\ValueObject\Link\LinkDecoration;
use DateInterval;
use DateTime;
use Deeplink\Entity\Deeplink;
use Deeplink\Entity\Target;
use Deeplink\View\Helper\DeeplinkLink;
use Doctrine\ORM\EntityManager;
use JetBrains\PhpStorm\Pure;
use Laminas\View\HelperPluginManager;

use function count;
use function sprintf;

class DeeplinkService extends AbstractService
{
    #[Pure] public function __construct(
        EntityManager $entityManager,
        private readonly HelperPluginManager $viewHelperManager
    ) {
        parent::__construct(entityManager: $entityManager);
    }

    public function findDeeplinkByHash(string $hash): ?Deeplink
    {
        return $this->entityManager->getRepository(entityName: Deeplink::class)->findOneBy(criteria: ['hash' => $hash]);
    }

    public function findTargets(): array
    {
        return $this->entityManager->getRepository(entityName: Target::class)->findTargets();
    }

    public function deleteInactiveDeeplinks(): void
    {
        $this->entityManager->getRepository(entityName: Deeplink::class)->deleteInactiveDeeplinks();
    }

    public function targetCanBeDeleted(Target $target): bool
    {
        $cannotBeDeleted = [];

        if (count($this->findActiveDeeplinksByTarget(target: $target))) {
            $cannotBeDeleted[] = 'This target has active deeplinks';
        }

        return count($cannotBeDeleted) === 0;
    }

    public function findActiveDeeplinksByTarget(Target $target): array
    {
        return $this->entityManager->getRepository(entityName: Deeplink::class)->findActiveDeeplinksByTarget(
            target: $target
        );
    }

    public function createDeeplink(
        Target $target,
        User $user,
        $keyId = null,
        $days = null
    ): Deeplink {
        /**
         * Produce the endDate
         */
        $endDate = new DateTime();
        $endDate->add(
            interval: new DateInterval(duration: sprintf('P%dD', $days ?? Deeplink::EXPIRATION_DAYS_DEFAULT))
        );
        /**
         * Create the deepLink
         */
        $deeplink = new Deeplink();
        $deeplink->setTarget(target: $target);
        $deeplink->setDateCreated(dateCreated: new DateTime());
        $deeplink->setEndDate(endDate: $endDate);
        $deeplink->setKeyId(keyId: $keyId);
        $deeplink->setUser(user: $user);

        $this->save(entity: $deeplink);

        return $deeplink;
    }

    public function createTargetFromRoute(string $route, ?string $name = null): Target
    {
        /** @var Target|null $target */
        $target = $this->entityManager->getRepository(entityName: Target::class)->findOneBy(
            criteria: ['route' => $route]
        );
        if (null !== $target) {
            return $target;
        }

        $target = new Target();
        $target->setTarget(target: sprintf("Target created from %s", $route));

        if (!empty($name)) {
            $target->setTarget(target: $name);
        }
        $target->setRoute(route: $route);
        $this->save(entity: $target);

        return $target;
    }

    public function parseDeeplinkUrl(Deeplink $deeplink, string $show = LinkDecoration::SHOW_RAW): string
    {
        $deeplinkLink = $this->viewHelperManager->get(name: DeeplinkLink::class);

        return $deeplinkLink($deeplink, 'view', $show);
    }
}

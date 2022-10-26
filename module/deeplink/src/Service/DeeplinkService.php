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
    #[Pure] public function __construct(EntityManager $entityManager, private readonly HelperPluginManager $viewHelperManager)
    {
        parent::__construct($entityManager);
    }

    public function findDeeplinkByHash(string $hash): ?Deeplink
    {
        return $this->entityManager->getRepository(Deeplink::class)->findOneBy(['hash' => $hash]);
    }

    public function findTargets(): array
    {
        return $this->entityManager->getRepository(Target::class)->findTargets();
    }

    public function deleteInactiveDeeplinks(): void
    {
        $this->entityManager->getRepository(Deeplink::class)->deleteInactiveDeeplinks();
    }

    public function targetCanBeDeleted(Target $target): bool
    {
        $cannotBeDeleted = [];

        if (count($this->findActiveDeeplinksByTarget($target))) {
            $cannotBeDeleted[] = 'This target has active deeplinks';
        }

        return count($cannotBeDeleted) === 0;
    }

    public function findActiveDeeplinksByTarget(Target $target): array
    {
        return $this->entityManager->getRepository(Deeplink::class)->findActiveDeeplinksByTarget($target);
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
        $endDate->add(new DateInterval(sprintf('P%dD', $days ?? Deeplink::EXPIRATION_DAYS_DEFAULT)));
        /**
         * Create the deepLink
         */
        $deeplink = new Deeplink();
        $deeplink->setTarget($target);
        $deeplink->setDateCreated(new DateTime());
        $deeplink->setEndDate($endDate);
        $deeplink->setKeyId($keyId);
        $deeplink->setUser($user);

        $this->save($deeplink);

        return $deeplink;
    }

    public function createTargetFromRoute(string $route, ?string $name = null): Target
    {
        /** @var Target $target */
        $target = $this->entityManager->getRepository(Target::class)->findOneBy(['route' => $route]);
        if (null !== $target) {
            return $target;
        }

        $target = new Target();
        $target->setTarget(sprintf("Target created from %s", $route));

        if (! empty($name)) {
            $target->setTarget($name);
        }
        $target->setRoute($route);
        $this->save($target);

        return $target;
    }

    public function parseDeeplinkUrl(Deeplink $deeplink, string $show = LinkDecoration::SHOW_RAW): string
    {
        $deeplinkLink = $this->viewHelperManager->get(DeeplinkLink::class);

        return $deeplinkLink($deeplink, 'view', $show);
    }
}

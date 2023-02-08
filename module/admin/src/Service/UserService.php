<?php

declare(strict_types=1);

namespace Admin\Service;

use Admin\Entity\Role;
use Admin\Entity\User;
use Api\Entity\OAuth\Service;
use Application\Service\AbstractService;
use Application\ValueObject\OAuth2\GenericUser;
use Cluster\Entity\Country;
use Cluster\Entity\Funder;
use Doctrine\ORM\EntityManager;
use Exception;
use Jield\Authorize\Role\UserAsRoleInterface;
use Jield\Authorize\Service\AccessRolesByUserInterface;
use Laminas\ApiTools\MvcAuth\Identity\GuestIdentity;
use Laminas\Crypt\Password\Bcrypt;
use Mailing\Service\EmailService;

use function sprintf;

class UserService extends AbstractService implements AccessRolesByUserInterface
{
    public function __construct(
        protected EntityManager $entityManager,
        private readonly EmailService $emailService
    ) {
        parent::__construct(entityManager: $entityManager);
    }

    public function findUserById(int $id): ?User
    {
        return $this->entityManager->find(className: User::class, id: $id);
    }

    public function findOrCreateUserFromGenericUser(GenericUser $genericUser, Service $service): User
    {
        //Try to see if we already have the user
        $user = $this->entityManager->getRepository(entityName: User::class)->findOneBy(
            criteria: [
                'email' => $genericUser->getEmail(),
            ]
        );

        if (null === $user) {
            $user = new User();
            $user->setEmail(email: $genericUser->getEmail());
        }

        $user->setFirstName(firstName: $genericUser->getFirstName());
        $user->setLastName(lastName: $genericUser->getLastName());

        $this->save(entity: $user);

        //Save the EurekaSecretariatOfficeStaff
        $user->setIsEurekaSecretariatStaffMember(
            isEurekaSecretariatStaffMember: $genericUser->isEurekaSecretariatStaffMember()
        );

        //Delete the funder object when the user is not a funder
        if (!$genericUser->isFunder() && $user->isFunder()) {
            $this->delete(abstractEntity: $user->getFunder());
        }

        if ($genericUser->isFunder()) {
            //Handle the funder

            $funder = $user->getFunder();

            $country = $this->entityManager->getRepository(entityName: Country::class)->findOneBy(
                criteria: [
                    'cd' => $genericUser->getFunderCountry(),
                ]
            );

            if (null === $country) {
                throw new Exception(
                    message: sprintf(
                        'Error Country with Alpha 2 code "%s" not found',
                        $genericUser->getFunderCountry()
                    ),
                    code: 1
                );
            }

            if (null === $funder) {
                $funder = new Funder();
                $funder->setUser(user: $user);
            }

            $funder->setCountry(country: $country);
            $this->save(entity: $funder);

            $this->updateFunderClusterPermissions(
                funder: $funder,
                service: $service
            );
        }

        return $user;
    }

    private function updateFunderClusterPermissions(
        Funder $funder,
        Service $service,
    ): void {
        //Each service is connected to a cluster, and we store this information in the funder object

        // get the ClusterPermissions from generic User
        //$currentFunderClusters = $funder->getClusters()->toArray();
        foreach ($service->getAllowedClusters() as $allowedCluster) {
            if ($funder->getClusters()->contains($allowedCluster)) {
                continue;
            }

            $funder->getClusters()->add($allowedCluster);
        }

        $this->save(entity: $funder);
    }

    public function getAccessRolesByUser(UserAsRoleInterface|User|GuestIdentity $user): array
    {
        if ($user instanceof GuestIdentity) {
            return [Role::ROLE_PUBLIC];
        }

        return $user->getRolesAsArray();
    }

    public function findUserByEmail(string $email): ?User
    {
        return $this->entityManager->getRepository(entityName: User::class)->findOneBy(criteria: ['email' => $email]);
    }

    public function lostPassword(string $emailAddress): void
    {
        //Find the user
        $user = $this->findUserByEmail(email: $emailAddress);
        if (null === $user) {
            return;
        }

        //Send the email
        $emailBuilder = $this->emailService->createNewTransactionalEmailBuilder(
            transactionalOrKey: '/user/lost-password'
        );
        $emailBuilder->addUserTo(user: $user);
        $emailBuilder->setDeeplink(route: 'user/change-password', user: $user);

        $this->emailService->send(emailBuilder: $emailBuilder);
    }

    public function updatePasswordForUser(string $password, User $user): bool
    {
        $Bcrypt = new Bcrypt();
        $Bcrypt->setCost(cost: 14);
        $pass = $Bcrypt->create(password: $password);
        $user->setPassword(password: $pass);
        $this->save(entity: $user);

        return true;
    }
}

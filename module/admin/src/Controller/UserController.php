<?php

declare(strict_types=1);

namespace Admin\Controller;

use Admin\Entity\User;
use Admin\Form\User\GenerateTokenForm;
use Admin\Form\User\Login;
use Admin\Form\User\LostPassword;
use Admin\Form\User\Password;
use Admin\Form\UserFilter;
use Admin\Service\AdminService;
use Admin\Service\OAuth2Service;
use Admin\Service\UserService;
use Api\Entity\OAuth\Client;
use Application\Authentication\Adapter\DatabaseAdapter;
use Application\Controller\Plugin\GetFilter;
use Application\Service\FormService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use Laminas\Authentication\AuthenticationService;
use Laminas\Http\Response;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Laminas\Paginator\Paginator;
use Laminas\Session\Container;
use Laminas\View\Model\ViewModel;
use OAuth2\Encryption\Jwt;
use function ceil;
use function sprintf;
use function str_starts_with;
use function urldecode;

/**
 * @method GetFilter getFilter()
 * @method User identity()
 * @method FlashMessenger flashMessenger()
 */
final class UserController extends AbstractActionController
{
    public function __construct(
        private readonly AdminService          $adminService,
        private readonly UserService           $userService,
        private readonly OAuth2Service         $oAuth2Service,
        private readonly array                 $config,
        private readonly EntityManager         $entityManager,
        private readonly AuthenticationService $authenticationService,
        private readonly FormService           $formService,
        private readonly TranslatorInterface   $translator
    )
    {
    }

    public function listAction(): ViewModel
    {
        $page         = $this->params()->fromRoute(param: 'page', default: 1);
        $filterPlugin = $this->getFilter();

        $userQuery = $this->adminService->findFiltered(
            entity: User::class,
            formResult: $filterPlugin->getFilter()
        );

        $paginator = new Paginator(
            adapter: new PaginatorAdapter(paginator: new ORMPaginator(query: $userQuery, fetchJoinCollection: false))
        );
        $paginator::setDefaultItemCountPerPage(count: 25);
        $paginator->setCurrentPageNumber(pageNumber: $page);
        $paginator->setPageRange(
            pageRange: ceil(
                num: $paginator->getTotalItemCount() / $paginator::getDefaultItemCountPerPage()
            )
        );

        $form = new UserFilter(entityManager: $this->entityManager);
        $form->setData(data: $filterPlugin->getFilterFormData());

        return new ViewModel(
            variables: [
                'paginator' => $paginator,
                'form'      => $form,
                'order'     => $filterPlugin->getOrder(),
                'direction' => $filterPlugin->getDirection(),
            ]
        );
    }

    public function viewAction(): ViewModel
    {
        $user = $this->adminService->find(entity: User::class, id: (int)$this->params('id'));

        if (null === $user) {
            return $this->notFoundAction();
        }

        return new ViewModel(variables: ['user' => $user]);
    }

    public function editAction(): Response|ViewModel
    {
        /** @var User $user */
        $user = $this->adminService->find(entity: User::class, id: (int)$this->params('id'));

        if (null === $user) {
            return $this->notFoundAction();
        }

        $data = $this->getRequest()->getPost()->toArray();
        $form = $this->formService->prepare(classNameOrEntity: User::class, data: $data);
        $form->bind($user);

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute(route: 'zfcadmin/user/view', params: ['id' => $user->getId()]);
            }

            if (isset($data['delete'])) {
                $user->setDateEnd(new \DateTime());
                $this->adminService->save($user);

                return $this->redirect()->toRoute('zfcadmin/user/list');
            }

            if ($form->isValid()) {
                /** @var User $user */
                $user = $form->getData();

                $user = $this->userService->save($user);

                $this->flashMessenger()->addSuccessMessage(
                    $this->translator->translate(message: "txt-user-has-been-updated-successfully")
                );

                $this->redirect()->toRoute(
                    route: 'zfcadmin/user/view',
                    params: [
                        'id' => $user->getId(),
                    ]
                );
            }
        }

        return new ViewModel(['form' => $form, 'user' => $user]);
    }

    public function generateTokenAction(): ViewModel|Response
    {
        /** @var User $user */
        $user = $this->adminService->find(entity: User::class, id: (int)$this->params('id'));

        if (null === $user) {
            return $this->notFoundAction();
        }

        $data = $this->getRequest()->getPost()->toArray();

        $form = new GenerateTokenForm(entityManager: $this->entityManager);
        $form->setData(data: $data);

        $token = null;

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('admin/user/view', ['id' => $user->getId()]);
            }

            if ($form->isValid()) {
                /** @var Client $client */
                $client  = $this->oAuth2Service->findClientByClientId($data['client']);
                $payload = $this->userService->generatePayload(client: $client, user: $user, algorithm: 'RS256');

                //We need to generate a token
                $token = (new Jwt())->encode(
                    payload: $payload,
                    key: $client->getPublicKey()?->getPrivateKey(),
                    algo: $client->getPublicKey()?->getEncryptionAlgorithm()
                );
            }
        }

        return new ViewModel(variables: ['user' => $user, 'form' => $form, 'token' => $token]);
    }

    public function lostPasswordAction(): Response|ViewModel
    {
        // if the user is logged in, we don't need to require a new password
        if ($this->identity()) {
            // redirect to the login redirect route
            return $this->redirect()->toRoute(route: 'user/login');
        }

        $form = new LostPassword();
        $data = $this->getRequest()->getPost()->toArray();

        $form->setData(data: $data);

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute(route: 'home');
            }

            if ($form->isValid()) {
                $formData = $form->getData();
                $this->userService->lostPassword(emailAddress: $formData['email']);
                $this->flashMessenger()
                    ->addSuccessMessage(
                        message: sprintf(
                            $this->translator->translate(message: "txt-new-password-for-%s-has-been-requested"),
                            $formData['email']
                        )
                    );

                return $this->redirect()->toRoute(route: 'user/login');
            }
        }

        return new ViewModel(variables: ['form' => $form]);
    }

    public function changePasswordAction(): Response|ViewModel
    {
        $form = new Password();
        $data = $this->getRequest()->getPost()->toArray();

        $form->setData(data: $data);
        if ($this->getRequest()->isPost() && $form->isValid()) {
            $formData = $form->getData();
            if (
                $this->userService->updatePasswordForUser(password: $formData['password'], user: $this->identity())
            ) {
                $this->flashMessenger()->addSuccessMessage(
                    message: $this->translator->translate(message: "txt-password-successfully-been-updated")
                );

                return $this->redirect()->toRoute(route: 'user/profile');
            }
        }

        return new ViewModel(variables: ['form' => $form]);
    }

    public function loginAction(): Response|ViewModel
    {
        $session = new Container(name: 'session');

        $form     = new Login();
        $data     = $this->getRequest()->getPost()->toArray();
        $redirect = $session->redirect;

        $isoAuthLogin = false;

        if (null !== $redirect && str_starts_with(haystack: $redirect, needle: '/oauth/')) {
            $isoAuthLogin = true;
        }

        $form->setData(data: $data);

        if ($this->getRequest()->isPost() && $form->isValid()) {
            //Grab the filtered values from the input filter
            $username = $form->getInputFilter()->getValue(name: 'username');
            $password = $form->getInputFilter()->getValue(name: 'password');

            $authAdapter  = new DatabaseAdapter(
                userService: $this->userService,
                username: $username,
                password: $password
            );
            $authenticate = $this->authenticationService->authenticate(adapter: $authAdapter);

            if ($authenticate->isValid()) {
                if (null !== $redirect) {
                    //Reset the session
                    $session->redirect = null;

                    return $this->redirect()->toUrl(url: urldecode(string: $redirect));
                }

                return $this->redirect()->toRoute(route: 'home');
            }

            $form->get(elementOrFieldset: 'password')->setMessages(messages: $authenticate->getMessages());
        }

        if ($isoAuthLogin) {
            $this->layout(template: 'layout/oauth');
        }

        return new ViewModel(
            variables: [
                'form'           => $form,
                'redirect'       => $redirect,
                'isoAuthLogin'   => $isoAuthLogin,
                'oauth2Settings' => $this->config['oauth2-settings'] ?? [],
            ]
        );
    }

    public function logoutAction(): Response
    {
        $this->authenticationService->clearIdentity();

        return $this->redirect()->toRoute(route: 'user/login');
    }
}

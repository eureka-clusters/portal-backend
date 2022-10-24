<?php

declare(strict_types=1);

namespace Admin\Controller;

use Admin\Entity\User;
use Admin\Form\Login;
use Admin\Form\LostPassword;
use Admin\Form\Password;
use Admin\Form\UserFilter;
use Admin\Service\AdminService;
use Admin\Service\UserService;
use Application\Authentication\Adapter\DatabaseAdapter;
use Application\Controller\Plugin\GetFilter;
use Application\Controller\Plugin\Preferences;
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

use function Admin\Controller\str_starts_with;
use function ceil;
use function sprintf;

/**
 * @method GetFilter getFilter()
 * @method User identity()
  * @method FlashMessenger flashMessenger()
 */
final class 
UserController extends AbstractActionController
{
    public function __construct(
        private readonly AdminService $adminService,
        private readonly UserService $userService,
        private readonly array $config,
        private readonly EntityManager $entityManager,
        private readonly AuthenticationService $authenticationService,
        private readonly TranslatorInterface $translator
    ) {
    }

    public function listAction(): ViewModel
    {
        $page = $this->params()->fromRoute('page', 1);
        $filterPlugin = $this->getFilter();

        $filterPlugin->setFilterByKey('status', User::STATUS_ACTIVE);

        $userQuery = $this->adminService->findFilteredByUser(
            User::class,
            $filterPlugin->getFilter(),
            $this->identity()
        );

        $paginator = new Paginator(
            new PaginatorAdapter(paginator: new ORMPaginator($userQuery, fetchJoinCollection: false))
        );
        $paginator::setDefaultItemCountPerPage($this->preferences()->getItemsPerPage());
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator::getDefaultItemCountPerPage()));

        $form = new UserFilter($this->entityManager);
        $form->setData($filterPlugin->getFilterFormData());

        return new ViewModel(
            [
                'paginator' => $paginator,
                'form' => $form,

                'order' => $filterPlugin->getOrder(),
                'direction' => $filterPlugin->getDirection(),
            ]
        );
    }

    public function lostPasswordAction(): Response|ViewModel
    {
        // if the user is logged in, we don't need to require a new password
        if ($this->identity()) {
            // redirect to the login redirect route
            return $this->redirect()->toRoute('user/login');
        }

        $form = new LostPassword();
        $data = $this->getRequest()->getPost()->toArray();

        $form->setData($data);

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute('home');
            }

            if ($form->isValid()) {
                $formData = $form->getData();
                $this->userService->lostPassword($formData['email']);
                $this->flashMessenger()
                    ->addSuccessMessage(
                        sprintf(
                            $this->translator->translate("txt-new-password-for-%s-has-been-requested"),
                            $formData['email']
                        )
                    );

                return $this->redirect()->toRoute('user/login');
            }
        }

        return new ViewModel(['form' => $form]);
    }

    public function changePasswordAction(): Response|ViewModel
    {
        $form = new Password();
        $data = $this->getRequest()->getPost()->toArray();

        $form->setData($data);
        if ($this->getRequest()->isPost() && $form->isValid()) {
            $formData = $form->getData();
            if (
                $this->userService->updatePasswordForUser($formData['password'], $this->identity())
            ) {
                $this->flashMessenger()->addSuccessMessage(
                    $this->translator->translate("txt-password-successfully-been-updated")
                );

                return $this->redirect()->toRoute('user/profile');
            }
        }

        return new ViewModel(['form' => $form]);
    }

    public function loginAction(): Response|ViewModel
    {
        $session = new Container('session');

        $form = new Login();
        $data = $this->getRequest()->getPost()->toArray();
        $redirect = $session->redirect;

        $isoAuthLogin = false;

        if (null !== $redirect && str_starts_with(haystack: $redirect, needle: '/oauth/')) {
            $isoAuthLogin = true;
        }

        $form->setData($data);

        if ($this->getRequest()->isPost() && $form->isValid()) {
            //Grab the filtered values from the input filter
            $username = $form->getInputFilter()->getValue('username');
            $password = $form->getInputFilter()->getValue('password');

            $authAdapter = new DatabaseAdapter($this->userService, $username, $password);
            $authenticate = $this->authenticationService->authenticate($authAdapter);

            if ($authenticate->isValid()) {
                if (null !== $redirect) {
                    //Reset the session
                    $session->redirect = null;

                    return $this->redirect()->toUrl(urldecode($redirect));
                }

                return $this->redirect()->toRoute('home');
            }

            $form->get('password')->setMessages($authenticate->getMessages());
        }

        if ($isoAuthLogin) {
            $this->layout(template: 'layout/oauth');
        }

        return new ViewModel(
            [
                'form' => $form,
                'redirect' => $redirect,
                'isoAuthLogin' => $isoAuthLogin,
                'oauth2Settings' => $this->config['oauth2-settings'] ?? [],
            ]
        );
    }

    public function logoutAction(): Response
    {
        $this->authenticationService->clearIdentity();

        return $this->redirect()->toRoute('user/login');
    }
}

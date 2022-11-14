<?php

declare(strict_types=1);

namespace Admin\Controller\OAuth2;

use Admin\Entity\User;
use Admin\Form;
use Admin\Service\OAuth2Service;
use Api\Entity;
use Api\Entity\OAuth\Client;
use Api\Entity\OAuth\PublicKey;
use Api\Entity\OAuth\Scope;
use Application\Controller\Plugin\GetFilter;
use Application\Form\SearchFilter;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use Laminas\Crypt\Password\Bcrypt;
use Laminas\Http\Response;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\Math\Rand;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Laminas\Paginator\Paginator;
use Laminas\View\Helper\Identity;
use Laminas\View\Model\ViewModel;
use OAuth2\Encryption\Jwt;

use function array_merge;
use function base64_encode;
use function ceil;
use function openssl_pkey_export;
use function openssl_pkey_get_details;
use function openssl_pkey_new;
use function sha1;
use function substr;
use function time;

/**
 * @method GetFilter getFilter()
 * @method FlashMessenger flashMessenger()
 * @method Identity|User identity();
 */
final class ClientController extends AbstractActionController
{
    public function __construct(
        private readonly OAuth2Service $oAuth2Service,
        private readonly EntityManager $entityManager,
        private readonly TranslatorInterface $translator
    ) {
    }

    public function listAction(): ViewModel
    {
        $filterPlugin = $this->getFilter();

        $page = $this->params('page');

        $roleQuery = $this->oAuth2Service->findFiltered(entity: Client::class, formResult: $filterPlugin->getFilter());

        $paginator = new Paginator(
            adapter: new PaginatorAdapter(paginator: new ORMPaginator(query: $roleQuery, fetchJoinCollection: false))
        );
        $paginator::setDefaultItemCountPerPage(count: 25);
        $paginator->setCurrentPageNumber(pageNumber: $page);
        $paginator->setPageRange(pageRange: ceil(num: $paginator->getTotalItemCount() / $paginator::getDefaultItemCountPerPage()));

        $form = new SearchFilter();
        $form->setData($filterPlugin->getFilterFormData());

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
        /** @var Entity\OAuth\Client $client */
        $client = $this->oAuth2Service->findClientByClientId(clientId: $this->params('id'));

        if (null === $client) {
            return $this->notFoundAction();
        }

        $secret = $this->params('secret');

        if ($this->getRequest()->isPost()) {
            //Create a secret
            $secret         = Rand::getString(length: 255);
            $bCrypt         = new Bcrypt();
            $bCryptedSecret = $bCrypt->create(password: $secret);

            $client->setClientSecret(clientsecret: $bCryptedSecret);
            $client->setClientSecretTeaser(clientsecretTeaser: substr(string: $secret, offset: 0, length: 2) . '*****');

            //Create a private-public key
            $publicKey = $client->getPublicKey();

            if (null === $publicKey) {
                $publicKey = new PublicKey();
                $publicKey->setClient(client: $client);
            }

            $privateKey   = openssl_pkey_new();
            $publicKeyPem = openssl_pkey_get_details(key: $privateKey)['key'];
            openssl_pkey_export(key: $privateKey, output: $privateKeyPrem);

            $publicKey->setEncryptionAlgorithm(encryptionAlgorithm: 'RS256');
            $publicKey->setPrivateKey(privateKey: $privateKeyPrem);
            $publicKey->setPublicKey(publicKey: $publicKeyPem);

            $client->setPublicKey(publicKey: $publicKey);

            $this->oAuth2Service->save(entity: $client);
        }

        $jwtHelper = new Jwt();

        $payload = [
            'id'         => 1, // for BC (see #591)
            'jti'        => 1,
            'iss'        => 'portal-backend',
            'aud'        => $client->getClientId(),
            'sub'        => $this->identity()->getId(),
            'exp'        => (new DateTime())->add(interval: new DateInterval(duration: 'P1Y'))->getTimestamp(),
            'iat'        => time(),
            'token_type' => 'HS256',
            'scope'      => 'openid',
        ];

        $HS256Token = $jwtHelper->encode(
            payload: $payload,
            key: $client->getPublicKey()?->getPublicKey(),
            algo: 'HS256'
        );

        $payload = [
            'id'         => 1, // for BC (see #591)
            'jti'        => 1,
            'iss'        => 'portal-backend',
            'aud'        => $client->getClientId(),
            'sub'        => $this->identity()->getId(),
            'exp'        => (new DateTime())->add(interval: new DateInterval(duration: 'P1Y'))->getTimestamp(),
            'iat'        => time(),
            'token_type' => 'RS256',
            'scope'      => 'openid',
        ];

        $RS256Token = $jwtHelper->encode(
            payload: $payload,
            key: $client->getPublicKey()?->getPrivateKey(),
            algo: $client->getPublicKey()?->getEncryptionAlgorithm()
        );

        return new ViewModel(
            variables: [
                'client'                 => $client,
                'secret'                 => $secret,
                'base64EncodedPublicKey' => base64_encode(string: $client->getPublicKey()->getPublicKey()),
                'RS256Token'             => $RS256Token,
                'HS256Token'             => $HS256Token,
                'decodedRS256Token'      => $jwtHelper->decode(jwt: $RS256Token, key: $client->getPublicKey()?->getPublicKey()),
                'decodedHS256Token'      => $jwtHelper->decode(jwt: $HS256Token, key: $client->getPublicKey()?->getPublicKey()),
            ]
        );
    }

    public function newAction(): Response|ViewModel
    {
        $data = $this->getRequest()->getPost()->toArray();

        $form = new Form\OAuth2\Client($this->entityManager);
        $form->setData($data);
        $form->remove('delete');

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute(route: 'zfcadmin/oauth2/client/list');
            }

            if ($form->isValid()) {
                $client = new Client();
                $client->setClientId(clientId: sha1(string: Rand::getString(length: 255)));

                //Create a secret
                $secret         = Rand::getString(length: 255);
                $bCrypt         = new Bcrypt();
                $bCryptedSecret = $bCrypt->create(password: $secret);

                $client->setClientSecret(clientsecret: $bCryptedSecret);
                $client->setClientSecretTeaser(clientsecretTeaser: substr(string: $secret, offset: 0, length: 2) . '*****');
                $client->setName(name: $data['name']);
                $client->setDescription(description: $data['description']);
                $client->setGrantTypes(grantTypes: $data['grantTypes']);

                /** @var Entity\OAuth\Scope $scope */
                $scope = $this->oAuth2Service->find(entity: Scope::class, id: (int) $data['scope']);

                $client->setScope(scope: $scope);
                $client->setRedirectUri(redirectUri: $data['redirectUri']);

                //Create a private-public key
                $publicKey = new PublicKey();
                $publicKey->setClient(client: $client);

                $privateKey = openssl_pkey_new();

                $publicKeyPem = openssl_pkey_get_details(key: $privateKey)['key'];
                openssl_pkey_export(key: $privateKey, output: $privateKeyPrem);

                $publicKey->setEncryptionAlgorithm(encryptionAlgorithm: 'RS256');
                $publicKey->setPrivateKey(privateKey: $privateKeyPrem);
                $publicKey->setPublicKey(publicKey: $publicKeyPem);

                $client->setPublicKey(publicKey: $publicKey);

                $this->oAuth2Service->save(entity: $client);
                $this->flashMessenger()->addSuccessMessage(
                    message: $this->translator->translate(
                        message: "txt-contact-oauth2-client-has-been-created-successfully"
                    ),
                );

                return $this->redirect()->toRoute(
                    route: 'zfcadmin/oauth2/client/view',
                    params: [
                        'id'     => $client->getId(),
                        'secret' => $secret,
                    ]
                );
            }
        }

        return new ViewModel(variables: ['form' => $form]);
    }

    public function editAction(): Response|ViewModel
    {
        /** @var Entity\OAuth\Client $client */
        $client = $this->oAuth2Service->findClientByClientId(clientId: $this->params('id'));

        if (null === $client) {
            return $this->notFoundAction();
        }

        $data = array_merge(
            [
                'redirectUri' => $client->getRedirectUri(),
                'name'        => $client->getName(),
                'description' => $client->getDescription(),
                'scope'       => $client->getScope(),
                'grantTypes'  => $client->getGrantTypes(),
            ],
            $this->getRequest()->getPost()->toArray()
        );

        $form = new Form\OAuth2\Client($this->entityManager);
        $form->setData($data);
        $form->remove('delete');

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute(
                    route: 'zfcadmin/oauth2/client/view',
                    params: [
                        'id' => $client->getId(),
                    ]
                );
            }

            if ($form->isValid()) {
                $client->setGrantTypes(grantTypes: null);

                /** @var Entity\OAuth\Scope $scope */
                $scope = $this->oAuth2Service->find(entity: Scope::class, id: (int) $data['scope']);

                $client->setScope(scope: $scope);
                $client->setName(name: $data['name']);
                $client->setDescription(description: $data['description']);
                $client->setRedirectUri(redirectUri: $data['redirectUri']);
                $client->setGrantTypes(grantTypes: $data['grantTypes']);

                $this->oAuth2Service->save(entity: $client);
                $this->flashMessenger()->addSuccessMessage(
                    message: $this->translator->translate(
                        message: "txt-contact-oauth2-client-has-been-updated-successfully"
                    ),
                );

                return $this->redirect()->toRoute(
                    route: 'zfcadmin/oauth2/client/view',
                    params: [
                        'id' => $client->getId(),
                    ]
                );
            }
        }

        return new ViewModel(variables: ['form' => $form]);
    }
}

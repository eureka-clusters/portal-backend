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
use Contact\Entity\Contact;
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
use Search\Form\SearchFilter;

use function array_merge;
use function ceil;
use function sha1;
use function substr;

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

        $roleQuery = $this->oAuth2Service->findFiltered(Client::class, $filterPlugin->getFilter());

        $paginator = new Paginator(
            new PaginatorAdapter(paginator: new ORMPaginator($roleQuery, fetchJoinCollection: false))
        );
        $paginator::setDefaultItemCountPerPage(25);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(ceil($paginator->getTotalItemCount() / $paginator::getDefaultItemCountPerPage()));

        $form = new SearchFilter();
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

    public function viewAction(): ViewModel
    {
        /** @var Entity\OAuth\Client $client */
        $client = $this->oAuth2Service->findClientByClientId($this->params('id'));

        if (null === $client) {
            return $this->notFoundAction();
        }

        $secret = $this->params('secret');

        if ($this->getRequest()->isPost()) {
            //Create a secret
            $secret = Rand::getString(255);
            $bCrypt = new Bcrypt();
            $bCryptedSecret = $bCrypt->create($secret);

            $client->setClientSecret($bCryptedSecret);
            $client->setClientSecretTeaser(substr($secret, 0, 2) . '*****');

            //Create a private-public key
            $publicKey = $client->getPublicKey();

            if (null === $publicKey) {
                $publicKey = new PublicKey();
                $publicKey->setClient($client);
            }

            $privateKey = openssl_pkey_new();
            $publicKeyPem = openssl_pkey_get_details($privateKey)['key'];
            openssl_pkey_export($privateKey, $privateKeyPrem);

            $publicKey->setEncryptionAlgorithm('RS256');
            $publicKey->setPrivateKey($privateKeyPrem);
            $publicKey->setPublicKey($publicKeyPem);

            $client->setPublicKey($publicKey);

            $this->oAuth2Service->save($client);
        }

        $jwtHelper = new Jwt();

        $payload = [
            'id' => 1, // for BC (see #591)
            'jti' => 1,
            'iss' => 'solodb',
            'aud' => $client->getClientId(),
            'sub' => $this->identity()->getId(),
            'exp' => (new DateTime())->add(new DateInterval('P1Y'))->getTimestamp(),
            'iat' => time(),
            'token_type' => 'HS256',
            'scope' => 'openid'
        ];

        $HS256Token = $jwtHelper->encode(
            $payload,
            $client->getPublicKey()?->getPublicKey(),
            'HS256'
        );

        $payload = [
            'id' => 1, // for BC (see #591)
            'jti' => 1,
            'iss' => 'solodb',
            'aud' => $client->getClientId(),
            'sub' => $this->identity()->getId(),
            'exp' => (new DateTime())->add(new DateInterval('P1Y'))->getTimestamp(),
            'iat' => time(),
            'token_type' => 'RS256',
            'scope' => 'openid'
        ];

        $RS256Token = $jwtHelper->encode(
            $payload,
            $client->getPublicKey()?->getPrivateKey(),
            $client->getPublicKey()?->getEncryptionAlgorithm()
        );

        return new ViewModel(
            [
                'client' => $client,
                'secret' => $secret,
                'base64EncodedPublicKey' => base64_encode($client->getPublicKey()->getPublicKey()),
                'RS256Token' => $RS256Token,
                'HS256Token' => $HS256Token,
                'decodedRS256Token' => $jwtHelper->decode($RS256Token, $client->getPublicKey()?->getPublicKey()),
                'decodedHS256Token' => $jwtHelper->decode($HS256Token, $client->getPublicKey()?->getPublicKey())
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
                return $this->redirect()->toRoute('zfcadmin/oauth2/client/list');
            }

            if ($form->isValid()) {
                $client = new Client();
                $client->setClientId(sha1(Rand::getString(255)));

                //Create a secret
                $secret = Rand::getString(255);
                $bCrypt = new Bcrypt();
                $bCryptedSecret = $bCrypt->create($secret);

                $client->setClientSecret($bCryptedSecret);
                $client->setClientSecretTeaser(substr($secret, 0, 2) . '*****');
                $client->setName($data['name']);
                $client->setDescription($data['description']);
                $client->setGrantTypes($data['grantTypes']);

                /** @var Entity\OAuth\Scope $scope */
                $scope = $this->oAuth2Service->find(Scope::class, (int)$data['scope']);

                $client->setScope($scope);
                $client->setRedirectUri($data['redirectUri']);

                //Create a private-public key
                $publicKey = new PublicKey();
                $publicKey->setClient($client);

                $privateKey = openssl_pkey_new();

                $publicKeyPem = openssl_pkey_get_details($privateKey)['key'];
                openssl_pkey_export($privateKey, $privateKeyPrem);

                $publicKey->setEncryptionAlgorithm('RS256');
                $publicKey->setPrivateKey($privateKeyPrem);
                $publicKey->setPublicKey($publicKeyPem);

                $client->setPublicKey($publicKey);

                $this->oAuth2Service->save($client);
                $this->flashMessenger()->addSuccessMessage(
                    $this->translator->translate("txt-contact-oauth2-client-has-been-created-successfully"),
                );

                return $this->redirect()->toRoute(
                    'zfcadmin/oauth2/client/view',
                    [
                        'id' => $client->getId(),
                        'secret' => $secret,
                    ]
                );
            }
        }

        return new ViewModel(['form' => $form]);
    }

    public function editAction(): Response|ViewModel
    {
        /** @var Entity\OAuth\Client $client */
        $client = $this->oAuth2Service->findClientByClientId($this->params('id'));

        if (null === $client) {
            return $this->notFoundAction();
        }

        $data = array_merge(
            [
                'redirectUri' => $client->getRedirectUri(),
                'name' => $client->getName(),
                'description' => $client->getDescription(),
                'scope' => $client->getScope(),
                'grantTypes' => $client->getGrantTypes(),
            ],
            $this->getRequest()->getPost()->toArray()
        );

        $form = new Form\OAuth2\Client($this->entityManager);
        $form->setData($data);
        $form->remove('delete');

        if ($this->getRequest()->isPost()) {
            if (isset($data['cancel'])) {
                return $this->redirect()->toRoute(
                    'zfcadmin/oauth2/client/view',
                    [
                        'id' => $client->getId(),
                    ]
                );
            }

            if ($form->isValid()) {
                $client->setGrantTypes(null);

                /** @var Entity\OAuth\Scope $scope */
                $scope = $this->oAuth2Service->find(Scope::class, (int)$data['scope']);

                $client->setScope($scope);
                $client->setName($data['name']);
                $client->setDescription($data['description']);
                $client->setRedirectUri($data['redirectUri']);
                $client->setGrantTypes($data['grantTypes']);

                $this->oAuth2Service->save($client);
                $this->flashMessenger()->addSuccessMessage(
                    $this->translator->translate("txt-contact-oauth2-client-has-been-updated-successfully"),
                );

                return $this->redirect()->toRoute(
                    'zfcadmin/oauth2/client/view',
                    [
                        'id' => $client->getId(),
                    ]
                );
            }
        }

        return new ViewModel(['form' => $form]);
    }
}

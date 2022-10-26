<?php

declare(strict_types=1);

namespace Mailing\Service;

use Exception;
use DateTime;
use Deeplink\Service\DeeplinkService;
use Doctrine\ORM\EntityManager;
use InvalidArgumentException;
use Laminas\Authentication\AuthenticationService;
use Laminas\Mail\Transport\Sendmail;
use Laminas\Mail\Transport\Smtp;
use Laminas\Mail\Transport\SmtpOptions;
use Mailing\Builder\CustomEmailBuilder;
use Mailing\Builder\EmailBuilder;
use Mailing\Builder\MailingEmailBuilder;
use Mailing\Builder\TransactionalEmailBuilder;
use Mailing\Entity\EmailMessage;
use Mailing\Entity\EmailMessageEvent;
use Mailing\Entity\Mailer;
use Mailing\Entity\Mailing;
use Mailing\Entity\Transactional;
use Mailing\Validator\EmailValidator;
use Mailjet\Client;
use Mailjet\Resources;
use Psr\Container\ContainerInterface;
use SendGrid;
use SendGrid\Mail\Mail;

class EmailService
{
    private readonly EntityManager $entityManager;
    private readonly GraphMailService $graphMailService;
    private ?Mailer $mailer = null;

    public function __construct(private readonly ContainerInterface $container)
    {
        $this->entityManager = $container->get(EntityManager::class);
        $this->graphMailService = $this->container->get(GraphMailService::class);
    }

    public function createNewTransactionalEmailBuilder(string|Transactional $transactionalOrKey
    ): TransactionalEmailBuilder {
        /** @var MailingService $mailingService */
        $mailingService = $this->container->get(MailingService::class);

        if (is_string($transactionalOrKey)) {
            $transactional = $mailingService->findTransactionalByKey($transactionalOrKey);

            if (null === $transactional) {
                throw new InvalidArgumentException(
                    sprintf('Transactional email with key "%s" cannot be found', $transactionalOrKey)
                );
            }
        } else {
            $transactional = $transactionalOrKey;
        }

        $this->mailer = $transactional->getMailer();

        return new TransactionalEmailBuilder(
            $transactional,
            $mailingService,
            $this->container->get(DeeplinkService::class),
            $this->container->get(AuthenticationService::class)
        );
    }

    public function createNewMailingMailBuilder(Mailing $mailing): MailingEmailBuilder
    {
        $this->mailer = $mailing->getMailer();

        return new MailingEmailBuilder(
            $mailing,
            $this->container->get(MailingService::class),
            $this->container->get(DeeplinkService::class),
            $this->container->get(AuthenticationService::class)
        );
    }

    public function createNewCustomEmailBuilder(Mailer $mailer): CustomEmailBuilder
    {
        $this->mailer = $mailer;

        return new CustomEmailBuilder(
            mailingService: $this->container->get(MailingService::class)
        );
    }

    public function send(EmailBuilder $emailBuilder): ?EmailMessage
    {
        $emailBuilder->renderEmail();

        $validator = new EmailValidator($emailBuilder);

        if (!$validator->isValid()) {
            return null;
        }

        if (!$this->mailer->isActive()) {
            return null;
        }

        $emailMessage = $this->registerEmailMessage(emailBuilder: $emailBuilder, mailer: $this->mailer);

        $emailMessageEvent = new EmailMessageEvent();
        $emailMessageEvent->setEmailMessage($emailMessage);

        if (!$this->mailer->isDevelopment()) {
            switch (true) {
                case $this->mailer->isGraph():
                    $this->sendEmailViaGraph(
                        emailBuilder: $emailBuilder,
                        emailMessage: $emailMessage,
                        emailMessageEvent: $emailMessageEvent
                    );
                    break;
                case $this->mailer->isSendGrid():
                    $this->sendEmailViaSendGrid(
                        emailBuilder: $emailBuilder,
                        emailMessage: $emailMessage,
                        emailMessageEvent: $emailMessageEvent
                    );
                    break;
                case $this->mailer->isSendmail():
                    $this->sendEmailViaSendmail(
                        emailBuilder: $emailBuilder,
                        emailMessage: $emailMessage,
                        emailMessageEvent: $emailMessageEvent
                    );
                    break;
                case $this->mailer->isMailjet():
                    $this->sendEmailViaMailjet(
                        emailBuilder: $emailBuilder,
                        emailMessage: $emailMessage,
                        emailMessageEvent: $emailMessageEvent
                    );
                    break;
                case $this->mailer->isSmtp():
                    $this->sendEmailViaSmtp(
                        emailBuilder: $emailBuilder,
                        emailMessage: $emailMessage,
                        emailMessageEvent: $emailMessageEvent
                    );
                    break;
                default:
                    throw new \InvalidArgumentException('The selected service does not exist');
            }
        }

        if ($this->mailer->isDevelopment()) {
            $result = 'sending_virtually_done_via_' . $this->mailer->getName();

            //Update the email message
            $emailMessage->setLatestEvent($result);
            $emailMessageEvent->setEvent($result);

            $emailMessage->setDateLatestEvent(new DateTime());

            $emailMessageEvent->setTime(new DateTime());
            $emailMessageEvent->setMessageId(0);

            $this->entityManager->persist($emailMessageEvent);
        }

        $this->entityManager->flush();

        return $emailMessage;
    }

    private function registerEmailMessage(EmailBuilder $emailBuilder, Mailer $mailer): EmailMessage
    {
        $emailMessage = new EmailMessage();
        $emailMessage->setMailer($mailer); //Inject the mailer here, otherwise the persist will create an empty version
        $emailMessage->setEmailAddress($emailBuilder->getSender()->getEmail());
        $emailMessage->setSubject($emailBuilder->getSubject());
        $emailMessage->setMessage($emailBuilder->getHtmlPart());
        $emailMessage->setAmountOfAttachments($emailBuilder->getAmountOfAttachments());

        $emailMessage->setSender($emailBuilder->getSender());
        $emailMessage->setTemplate($emailBuilder->getTemplate());
        $emailMessage->setTo($emailBuilder->getTo());
        $emailMessage->setCc($emailBuilder->getCC());
        $emailMessage->setBcc($emailBuilder->getBCC());

        if ($emailBuilder->hasMailingUser()) {
            $emailMessage->setMailingUser($emailBuilder->getMailingUser());
            $emailMessage->setUser($emailBuilder->getMailingUser()?->getUser());
        }

        if ($emailBuilder->hasDistributionListUser()) {
            $emailMessage->setDistributionListUser($emailBuilder->getDistributionListUser());
            $emailMessage->setUser($emailBuilder->getDistributionListUser()?->getUser());
        }

        $this->entityManager->persist($emailMessage);

        return $emailMessage;
    }

    private function sendEmailViaGraph(
        EmailBuilder $emailBuilder,
        EmailMessage $emailMessage,
        EmailMessageEvent $emailMessageEvent
    ): void {
        $this->graphMailService->testSendMail($emailBuilder);

        $result = 'sent_with_azure';

        //Update the email message
        $emailMessage->setLatestEvent($result);
        $emailMessageEvent->setEvent($result);

        $emailMessage->setDateLatestEvent(new DateTime());

        $emailMessageEvent->setTime(new DateTime());
        $emailMessageEvent->setMessageId(0);

        $this->entityManager->persist($emailMessageEvent);
    }

    private function sendEmailViaSendGrid(
        EmailBuilder $emailBuilder,
        EmailMessage $emailMessage,
        EmailMessageEvent $emailMessageEvent
    ): void {
        $email = new Mail();
        $email->setFrom(email: $emailBuilder->getSender()->getEmail(), name: $emailBuilder->getSender()->getSender());
        $email->setSubject(subject: $emailMessage->getSubject());
        foreach ($emailBuilder->getTo() as $to) {
            $email->addTo(to: $to['Email'], name: $to['Name']);
        }

        $email->addContent(type: "text/plain", value: $emailBuilder->getTextPart());
        $email->addContent(
            type: "text/html",
            value: $emailBuilder->getHtmlPart()
        );

        foreach ($emailBuilder->getAttachments() as $attachment) {
            $email->addAttachment(
                attachment: $attachment['Base64Content'],
                type: $attachment['ContentType'],
                filename: $attachment['Filename'],
                disposition: 'attachment'
            );
        }

        foreach ($emailBuilder->getInlinedAttachments() as $attachment) {
            $email->addAttachment(
                attachment: $attachment['Base64Content'],
                type: $attachment['ContentType'],
                filename: $attachment['Filename'],
                disposition: 'inline'
            );
        }

        $sendgrid = new SendGrid($this->mailer->getSendGridApiKey());
        try {
            $response = $sendgrid->send($email);

            //Update the email message
            $emailMessage->setLatestEvent('sent_to_sendgriddd');
            $emailMessageEvent->setEvent('sent_to_sendgrid');
            $emailMessageEvent->setSmtpReply($response->body());
        } catch (Exception $e) {
            //Update the email message
            $emailMessage->setLatestEvent('sending_failed');
            $emailMessageEvent->setEvent('sending_failed');
            $emailMessageEvent->setError($e->getMessage());
        }

        $emailMessage->setDateLatestEvent(new DateTime());

        $emailMessageEvent->setTime(new DateTime());
        $emailMessageEvent->setMessageId(0);
        $this->entityManager->persist($emailMessageEvent);
    }

    private function sendEmailViaSendmail(
        EmailBuilder $emailBuilder,
        EmailMessage $emailMessage,
        EmailMessageEvent $emailMessageEvent
    ): void {
        $transport = new Sendmail();

        $transport->send($emailBuilder->getMessage());
        $result = 'sent_via_sendmail';

        //Update the email message
        $emailMessage->setLatestEvent($result);
        $emailMessageEvent->setEvent($result);

        $emailMessage->setDateLatestEvent(new DateTime());

        $emailMessageEvent->setTime(new DateTime());
        $emailMessageEvent->setMessageId(0);

        $this->entityManager->persist($emailMessageEvent);
    }

    private function sendEmailViaMailjet(
        EmailBuilder $emailBuilder,
        EmailMessage $emailMessage,
        EmailMessageEvent $emailMessageEvent
    ): void {
        $client = new Client(
            key: $this->mailer->getUsername(),
            secret: $this->mailer->getPassword(),
            call: true,
            settings: ['version' => 'v3.1']
        );

        $response = $client->post(
            Resources::$Email,
            ['body' => $emailBuilder->getMailjetBody($emailMessage->getIdentifier())->toArray()]
        );

        if (!$response->success()) {
            //Update the email message
            $emailMessage->setLatestEvent('sending_failed');
            $emailMessageEvent->setEvent('sending_failed');
        }

        if ($response->success()) {
            //Update the email message
            $emailMessage->setLatestEvent('sent_to_mailjet');
            $emailMessageEvent->setEvent('sent_to_mailjet');
        }

        $emailMessage->setDateLatestEvent(new DateTime());

        $emailMessageEvent->setTime(new DateTime());
        $emailMessageEvent->setMessageId(0);
        $emailMessageEvent->setError($response->getReasonPhrase());
        $this->entityManager->persist($emailMessageEvent);
    }

    private function sendEmailViaSmtp(
        EmailBuilder $emailBuilder,
        EmailMessage $emailMessage,
        EmailMessageEvent $emailMessageEvent
    ): void {
        $transport = new Smtp();
        $transportConfig = [];

        $transportConfig['host'] = $this->mailer->getHostname();
        $transportConfig['port'] = $this->mailer->getPort();

        if (null !== $this->mailer->getSsl()) {
            $transportConfig['connection_config']['ssl'] = $this->mailer->getSsl();
        }
        if (null !== $this->mailer->getUsername()) {
            $transportConfig['connection_class'] = 'login';
            $transportConfig['connection_config']['username'] = $this->mailer->getUsername();
        }
        if (null !== $this->mailer->getPassword()) {
            $transportConfig['connection_class'] = 'login';
            $transportConfig['connection_config']['password'] = $this->mailer->getPassword();
        }

        $smtpOptions = new SmtpOptions($transportConfig);
        $transport->setOptions($smtpOptions);
        $transport->send($emailBuilder->getMessage());
        $result = $transport->getConnection()?->getResponse()[0] ?? 'sent_with_smtp';

        //Update the email message
        $emailMessage->setLatestEvent($result);
        $emailMessageEvent->setEvent($result);

        $emailMessage->setDateLatestEvent(new DateTime());

        $emailMessageEvent->setTime(new DateTime());
        $emailMessageEvent->setMessageId(0);

        $this->entityManager->persist($emailMessageEvent);
    }
}

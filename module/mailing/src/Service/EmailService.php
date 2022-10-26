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
    private ?Mailer $mailer = null;

    public function __construct(private readonly ContainerInterface $container)
    {
        $this->entityManager = $container->get(EntityManager::class);
    }

    public function createNewTransactionalEmailBuilder(string|Transactional $transactionalOrKey
    ): TransactionalEmailBuilder {
        /** @var MailingService $mailingService */
        $mailingService = $this->container->get(MailingService::class);

        if (is_string(value: $transactionalOrKey)) {
            $transactional = $mailingService->findTransactionalByKey(key: $transactionalOrKey);

            if (null === $transactional) {
                throw new InvalidArgumentException(
                    message: sprintf('Transactional email with key "%s" cannot be found', $transactionalOrKey)
                );
            }
        } else {
            $transactional = $transactionalOrKey;
        }

        $this->mailer = $transactional->getMailer();

        return new TransactionalEmailBuilder(
            transactional: $transactional,
            mailingService: $mailingService,
            deeplinkService: $this->container->get(DeeplinkService::class),
            authenticationService: $this->container->get(AuthenticationService::class)
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

        $validator = new EmailValidator(emailBuilder: $emailBuilder);

        if (!$validator->isValid()) {
            return null;
        }

        if (!$this->mailer->isActive()) {
            return null;
        }

        $emailMessage = $this->registerEmailMessage(emailBuilder: $emailBuilder, mailer: $this->mailer);

        $emailMessageEvent = new EmailMessageEvent();
        $emailMessageEvent->setEmailMessage(emailMessage: $emailMessage);

        if (!$this->mailer->isDevelopment()) {
            switch (true) {
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
                    throw new \InvalidArgumentException(message: 'The selected service does not exist');
            }
        }

        if ($this->mailer->isDevelopment()) {
            $result = 'sending_virtually_done_via_' . $this->mailer->getName();

            //Update the email message
            $emailMessage->setLatestEvent(latestEvent: $result);
            $emailMessageEvent->setEvent(event: $result);

            $emailMessage->setDateLatestEvent(dateLatestEvent: new DateTime());

            $emailMessageEvent->setTime(time: new DateTime());
            $emailMessageEvent->setMessageId(messageId: 0);

            $this->entityManager->persist(entity: $emailMessageEvent);
        }

        $this->entityManager->flush();

        return $emailMessage;
    }

    private function registerEmailMessage(EmailBuilder $emailBuilder, Mailer $mailer): EmailMessage
    {
        $emailMessage = new EmailMessage();
        $emailMessage->setMailer(mailer: $mailer); //Inject the mailer here, otherwise the persist will create an empty version
        $emailMessage->setEmailAddress(emailAddress: $emailBuilder->getSender()->getEmail());
        $emailMessage->setSubject(subject: $emailBuilder->getSubject());
        $emailMessage->setMessage(message: $emailBuilder->getHtmlPart());
        $emailMessage->setAmountOfAttachments(amountOfAttachments: $emailBuilder->getAmountOfAttachments());

        $emailMessage->setSender(sender: $emailBuilder->getSender());
        $emailMessage->setTemplate(template: $emailBuilder->getTemplate());
        $emailMessage->setTo(to: $emailBuilder->getTo());
        $emailMessage->setCc(cc: $emailBuilder->getCC());
        $emailMessage->setBcc(bcc: $emailBuilder->getBCC());

        if ($emailBuilder->hasMailingUser()) {
            $emailMessage->setMailingUser($emailBuilder->getMailingUser());
            $emailMessage->setUser(user: $emailBuilder->getMailingUser()?->getUser());
        }

        if ($emailBuilder->hasDistributionListUser()) {
            $emailMessage->setDistributionListUser($emailBuilder->getDistributionListUser());
            $emailMessage->setUser(user: $emailBuilder->getDistributionListUser()?->getUser());
        }

        $this->entityManager->persist(entity: $emailMessage);

        return $emailMessage;
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

        $sendgrid = new SendGrid(apiKey: $this->mailer->getSendGridApiKey());
        try {
            $response = $sendgrid->send(email: $email);

            //Update the email message
            $emailMessage->setLatestEvent(latestEvent: 'sent_to_sendgriddd');
            $emailMessageEvent->setEvent(event: 'sent_to_sendgrid');
            $emailMessageEvent->setSmtpReply(smtpReply: $response->body());
        } catch (Exception $e) {
            //Update the email message
            $emailMessage->setLatestEvent(latestEvent: 'sending_failed');
            $emailMessageEvent->setEvent(event: 'sending_failed');
            $emailMessageEvent->setError(error: $e->getMessage());
        }

        $emailMessage->setDateLatestEvent(dateLatestEvent: new DateTime());

        $emailMessageEvent->setTime(time: new DateTime());
        $emailMessageEvent->setMessageId(messageId: 0);
        $this->entityManager->persist(entity: $emailMessageEvent);
    }

    private function sendEmailViaSendmail(
        EmailBuilder $emailBuilder,
        EmailMessage $emailMessage,
        EmailMessageEvent $emailMessageEvent
    ): void {
        $transport = new Sendmail();

        $transport->send(message: $emailBuilder->getMessage());
        $result = 'sent_via_sendmail';

        //Update the email message
        $emailMessage->setLatestEvent(latestEvent: $result);
        $emailMessageEvent->setEvent(event: $result);

        $emailMessage->setDateLatestEvent(dateLatestEvent: new DateTime());

        $emailMessageEvent->setTime(time: new DateTime());
        $emailMessageEvent->setMessageId(messageId: 0);

        $this->entityManager->persist(entity: $emailMessageEvent);
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
            resource: Resources::$Email,
            args: ['body' => $emailBuilder->getMailjetBody(identifier: $emailMessage->getIdentifier())->toArray()]
        );

        if (!$response->success()) {
            //Update the email message
            $emailMessage->setLatestEvent(latestEvent: 'sending_failed');
            $emailMessageEvent->setEvent(event: 'sending_failed');
        }

        if ($response->success()) {
            //Update the email message
            $emailMessage->setLatestEvent(latestEvent: 'sent_to_mailjet');
            $emailMessageEvent->setEvent(event: 'sent_to_mailjet');
        }

        $emailMessage->setDateLatestEvent(dateLatestEvent: new DateTime());

        $emailMessageEvent->setTime(time: new DateTime());
        $emailMessageEvent->setMessageId(messageId: 0);
        $emailMessageEvent->setError(error: $response->getReasonPhrase());
        $this->entityManager->persist(entity: $emailMessageEvent);
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

        $smtpOptions = new SmtpOptions(options: $transportConfig);
        $transport->setOptions(options: $smtpOptions);
        $transport->send(message: $emailBuilder->getMessage());
        $result = $transport->getConnection()?->getResponse()[0] ?? 'sent_with_smtp';

        //Update the email message
        $emailMessage->setLatestEvent(latestEvent: $result);
        $emailMessageEvent->setEvent(event: $result);

        $emailMessage->setDateLatestEvent(dateLatestEvent: new DateTime());

        $emailMessageEvent->setTime(time: new DateTime());
        $emailMessageEvent->setMessageId(messageId: 0);

        $this->entityManager->persist(entity: $emailMessageEvent);
    }
}

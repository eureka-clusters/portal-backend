<?php

declare(strict_types=1);

namespace Mailing\Builder;

use Admin\Entity\User;
use Application\ValueObject\Link\LinkDecoration;
use DateTime;
use Deeplink\Service\DeeplinkService;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use InvalidArgumentException;
use JetBrains\PhpStorm\Pure;
use Laminas\Mail\AddressList;
use Laminas\Mail\Message;
use Laminas\Mime;
use Laminas\Mime\Part;
use Mailing\Entity\Sender;
use Mailing\Entity\Template;
use Mailing\Service\MailingService;
use Mailing\ValueObject;
use Mailing\ValueObject\Ical;
use Mailing\ValueObject\Mailjet\Attachment;
use Mailing\ValueObject\Mailjet\Body;
use Mailing\ValueObject\Mailjet\Email;
use Mailing\ValueObject\Recipient;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

use function base64_encode;
use function count;
use function sprintf;
use function strip_tags;

abstract class EmailBuilder
{
    protected ?string $subject = null;

    protected ?string $emailCampaign = null;

    protected ArrayCollection $templateVariables;

    protected ?string $textPart = null;

    protected ?string $htmlPart = null;

    protected Template $template;

    private Sender $sender;

    private ?Recipient $replyTo = null;

    private bool $personal = true;

    private Recipient $from;

    /** @var ValueObject\Recipient[] */
    private array $to = [];

    /** @var ValueObject\Recipient[] */
    private array $cc = [];

    /** @var ValueObject\Recipient[] */
    private array $bcc = [];

    /** @var ValueObject\Mailjet\Attachment[] */
    private array $attachments = [];

    /** @var ValueObject\Ical[] */
    private array $invitations = [];

    /** @var ValueObject\Mailjet\Attachment[] */
    private array $inlinedAttachments = [];

    /** @var ValueObject\Header[] */
    private array $headers = [];

    private DeeplinkService $deeplinkService;

    public function __construct(
        MailingService $mailingService,
        ?DeeplinkService $deeplinkService = null
    ) {
        $this->templateVariables = new ArrayCollection();

        $this->setSender(setSender: $mailingService->findDefaultSender());
        $this->setTemplate(template: $mailingService->findDefaultTemplate());

        if (null !== $deeplinkService) {
            $this->deeplinkService = $deeplinkService;
        }
    }

    public function getSender(): Sender
    {
        return $this->sender;
    }

    public function setSender(?Sender $setSender = null, ?User $ownerOrLoggedInUser = null): EmailBuilder
    {
        //Via this function it is possible to overrule the sender, but if no value for the sender is given
        //We do a fallback to the default sender
        if (null !== $setSender) {
            $this->sender = $setSender;
        }

        $sender = $this->sender;

        switch ($sender) {
            case $sender->isLoggedInUser():
            case $sender->isOwner():
                if (null !== $ownerOrLoggedInUser) {
                    $this->setTemplateVariables(
                        variables: [
                            'sender_email' => $ownerOrLoggedInUser->getEmail(),
                            'sender_name' => $ownerOrLoggedInUser->parseFullName(),
                        ]
                    );

                    $this->from = new Recipient(
                        name: $ownerOrLoggedInUser->parseFullName(),
                        email: $ownerOrLoggedInUser->getEmail()
                    );
                }

                break;
            default:
                $this->setTemplateVariables(
                    variables: [
                        'sender_email' => $sender->getEmail(),
                        'sender_name' => $sender->getSender(),
                    ]
                );

                $this->from = new Recipient(name: $sender->getSender(), email: $sender->getEmail());
                break;
        }

        return $this;
    }

    public function getMailjetBody(string $identifier): Body
    {
        $messages = [];

        $message = new Email(
            from: $this->from->toArray(),
            to: $this->getTo(),
            cc: $this->getCC(),
            bcc: $this->getBCC(),
            subject: $this->subject,
            textPart: $this->textPart,
            htmlPart: $this->htmlPart,
            customID: $identifier,
            eventPayload: '',
            replyTo: $this->getReplyTo(),
            trackOpens: 'enabled',
            trackClicks: 'enabled',
            customCampaign: $this->emailCampaign,
            attachments: $this->getAttachments(),
            inlinedAttachments: $this->getInlinedAttachments(),
            headers: $this->getHeaders()
        );
        $messages[] = $message->toArray();

        return new Body(messages: $messages);
    }

    #[Pure] public function getTo(): array
    {
        $to = [];
        foreach ($this->to as $singleTo) {
            $to[] = $singleTo->toArray();
        }

        return $to;
    }

    #[Pure] public function getCC(): array
    {
        $cc = [];
        foreach ($this->cc as $singleCC) {
            $cc[] = $singleCC->toArray();
        }

        return $cc;
    }

    #[Pure] public function getBCC(): array
    {
        $bcc = [];
        foreach ($this->bcc as $singleBCC) {
            $bcc[] = $singleBCC->toArray();
        }

        return $bcc;
    }

    #[Pure] public function getReplyTo(): ?array
    {
        return $this->replyTo?->toArray();
    }

    public function setReplyTo(string $replyToName, string $replyToEmail): EmailBuilder
    {
        $this->replyTo = new Recipient(name: $replyToName, email: $replyToEmail);

        return $this;
    }

    public function getAttachments(): ?array
    {
        $attachments = [];
        foreach ($this->attachments as $singleAttachment) {
            $attachments[] = $singleAttachment->toArray();
        }

        return $attachments;
    }

    public function getInlinedAttachments(): array
    {
        $inlinedAttachments = [];
        foreach ($this->inlinedAttachments as $singleAttachment) {
            $inlinedAttachments[] = $singleAttachment->toArray();
        }

        return $inlinedAttachments;
    }

    #[Pure] private function getHeaders(): array
    {
        $headers = [];
        foreach ($this->headers as $singleHeader) {
            $headers += $singleHeader->toArray();
        }

        return $headers;
    }

    public function setReplyToUser(User $user): EmailBuilder
    {
        $this->setReplyTo(replyToName: $user->parseFullName(), replyToEmail: $user->getEmail());

        return $this;
    }

    public function noReplyTo(): void
    {
        $this->replyTo = null;
    }

    public function addAttachment(string $contentType, string $fileName, string $content): void
    {
        $this->attachments[] = new Attachment(
            contentType: $contentType,
            fileName: $fileName,
            base64Content: base64_encode(string: $content),
            rawContent: $content
        );
    }

    public function addInvitation(
        DateTime $startDate,
        DateTime $endDate,
        string $title,
        string $summary,
        ?string $location,
        User $organiser
    ): void {
        $this->attachments[] = new Ical(
            startDate: $startDate,
            endDate: $endDate,
            title: $title,
            summary: $summary,
            location: $location,
            organiser: $organiser
        );
    }

    public function addUserTo(User $user): EmailBuilder
    {
        //As we add the to user in the builder, we automatically extract the user details in the template variables
        //Only extract user details when mailing is personal
        if ($this->personal) {
            $this->setTemplateVariables(
                variables: [
                    'firstname' => $user->getFirstName(),
                    'lastname' => $user->getLastName(),
                    'fullname' => $user->parseFullName(),
                    'email' => $user->getEmail(),
                ]
            );
        }

        $this->addTo(name: $user->parseFullName(), email: $user->getEmail());

        return $this;
    }

    public function setTemplateVariables(array $variables): EmailBuilder
    {
        foreach ($variables as $key => $value) {
            $this->setTemplateVariable(key: $key, value: $value);
        }

        return $this;
    }

    public function setTemplateVariable($key, $value): EmailBuilder
    {
        $this->templateVariables->set(key: $key, value: $value);

        return $this;
    }

    public function addTo(string $name, string $email): EmailBuilder
    {
        if ($this->personal && count($this->to) > 0) {
            throw new InvalidArgumentException(message: 'Impossible to add more recipients to an personal email');
        }

        $to = new Recipient(name: $name, email: $email);

        if ($to->isValid()) {
            $this->to[] = $to;
        }

        return $this;
    }

    public function addUserCC(User $user): EmailBuilder
    {
        $this->addCC(name: $user->parseFullName(), email: $user->getEmail());

        return $this;
    }

    public function addCC(string $name, string $email): EmailBuilder
    {
        if ($this->personal) {
            throw new InvalidArgumentException(message: 'Impossible to add CC recipients to an personal email');
        }

        $cc = new Recipient(name: $name, email: $email);

        if ($cc->isValid()) {
            $this->cc[] = $cc;
        }

        return $this;
    }

    public function addUserBCC(User $user): EmailBuilder
    {
        $this->addBCC(name: $user->parseFullName(), email: $user->getEmail());

        return $this;
    }

    public function addBCC(string $name, string $email): EmailBuilder
    {
        if ($this->personal) {
            throw  new InvalidArgumentException(message: 'Impossible to add BCC recipients to an personal email');
        }

        $bcc = new Recipient(name: $name, email: $email);

        if ($bcc->isValid()) {
            $this->bcc[] = $bcc;
        }

        return $this;
    }

    public function setPersonal(bool $personal): EmailBuilder
    {
        $this->personal = $personal;
        return $this;
    }

    public function getHtmlPart(): ?string
    {
        return $this->htmlPart;
    }

    public function getTextPart(): ?string
    {
        return $this->textPart;
    }

    public function getAmountOfAttachments(): int
    {
        return count($this->attachments);
    }

    public function getEmailCampaign(): ?string
    {
        return $this->emailCampaign;
    }

    public function getTemplate(): Template
    {
        return $this->template;
    }

    public function setTemplate(Template $template): EmailBuilder
    {
        $this->template = $template;
        return $this;
    }

    public function setDeeplink(string $route, User $user, int|string $key = null): void
    {
        if (!$this->personal) {
            throw new InvalidArgumentException(message: 'It is not possible to add a deeplink for a non-personal email');
        }
        //Create a target
        $target = $this->deeplinkService->createTargetFromRoute(route: $route);

        $deeplink = $this->deeplinkService->createDeeplink(
            target: $target,
            user: $user,
            keyId: $key
        );

        $this->setTemplateVariable(
            key: 'deeplink',
            value: $this->deeplinkService->parseDeeplinkUrl(
                deeplink: $deeplink,
                show: LinkDecoration::SHOW_RAW
            )
        );
    }

    abstract public function renderEmail(): void;

    public function getMessage(): Message
    {
        $message = new Message();
        $message->setFrom(emailOrAddressList: $this->from->toAddress());

        $message->setTo(emailOrAddressList: $this->getToAsAddressList());
        $message->setCc(emailOrAddressList: $this->getCCAsAddressList());
        $message->setBcc(emailOrAddressList: $this->getBccAsAddressList());

        if (null !== $this->replyTo) {
            $message->setReplyTo(emailOrAddressList: $this->replyTo->toAddress());
        }

        $message->setSubject(subject: $this->subject);

        $html = new Part(content: $this->htmlPart);
        $html->setType(type: Mime\Mime::TYPE_HTML);
        $html->setCharset(charset: 'utf-8');
        $html->setEncoding(encoding: Mime\Mime::ENCODING_QUOTEDPRINTABLE);

        $plain = new Part(content: 'TEXT' . $this->textPart);
        $plain->setCharset(charset: 'utf-8');
        $plain->setType(type: Mime\Mime::TYPE_TEXT);
        $plain->setEncoding(encoding: Mime\Mime::ENCODING_QUOTEDPRINTABLE);

        $content = new Mime\Message();
        $content->setParts(
            parts: [
                $plain,
                $html,
            ]
        );

        if (!$this->hasMultiParts()) {
            $message->setBody(body: $content);
            $contentTypeHeader = $message->getHeaders()->get(name: 'Content-Type');
            /** @phpstan-ignore-next-line */
            $contentTypeHeader->setType(type: Mime\Mime::MULTIPART_ALTERNATIVE);

            return $message;
        }

        $multiParts = [];
        $contentPart = new Part(content: $content->generateMessage());
        $contentPart->setType(type: Mime\Mime::MULTIPART_ALTERNATIVE);
        $contentPart->setBoundary(boundary: $content->getMime()->boundary());

        $multiParts[] = $contentPart;
        foreach ($this->attachments as $attachment) {
            $multiParts[] = $attachment->toMimePart();
        }

        foreach ($this->inlinedAttachments as $attachment) {
            $multiParts[] = $attachment->toMimePart(inline: true);
        }

        foreach ($this->invitations as $invitation) {
            $multiParts[] = $invitation->toMimePart();
        }

        $body = new Mime\Message();
        $body->setParts(parts: $multiParts);

        $message->setBody(body: $body);

        $contentTypeHeader = $message->getHeaders()->get(name: 'Content-Type');
        /** @phpstan-ignore-next-line */
        $contentTypeHeader->setType(type: Mime\Mime::MULTIPART_RELATED);

        return $message;
    }

    public function getToAsAddressList(): AddressList
    {
        $to = new AddressList();
        foreach ($this->to as $singleTo) {
            $to->add(emailOrAddress: $singleTo->toAddress());
        }

        return $to;
    }

    public function getCCAsAddressList(): AddressList
    {
        $cc = new AddressList();
        foreach ($this->cc as $singleCC) {
            $cc->add(emailOrAddress: $singleCC->toAddress());
        }

        return $cc;
    }

    public function getBCCAsAddressList(): AddressList
    {
        $bcc = new AddressList();
        foreach ($this->bcc as $singleBCC) {
            $bcc->add(emailOrAddress: $singleBCC->toAddress());
        }

        return $bcc;
    }

    public function hasMultiParts(): bool
    {
        return count($this->attachments) > 0 || count($this->invitations) > 0;
    }



    protected function renderSubject(string $mailSubject): void
    {
        try {
            //Create a Twig Template on the fly with the template source content
            $subjectTemplate = new Environment(
                loader: new ArrayLoader(
                    templates: ['template_subject' => $this->template->getSubject()]
                )
            );
            //Create a second template in which the content of the email is parsed and render the content in
            $mailSubject = (new Environment(
                loader: new ArrayLoader(
                    templates: ['email_subject' => $mailSubject]
                )
            ))->render(name: 'email_subject', context: $this->templateVariables->toArray());

            $this->setTemplateVariable(key: 'subject', value: $mailSubject);

            //Render the $mailBody in the content of the main template
            $this->subject = $subjectTemplate->render(
                name: 'template_subject',
                context: $this->templateVariables->toArray()
            );
        } catch (Exception $e) {
            $this->subject = sprintf(
                'Something went wrong with the merge of the subject. Error message: %s',
                $e->getMessage()
            );
        }
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    protected function renderBody(string $bodyText): void
    {
        try {
            //Create a Twig Template on the fly with the template source content
            $htmlTemplate = new Environment(
                loader: new ArrayLoader(
                    templates: [$this->template->parseName() => $this->template->parseSourceContent()]
                )
            );

            //Create a second template in which the content of the email is parsed and render the content in
            $mailBody = (new Environment(
                loader: new ArrayLoader(
                    templates: ['email_content' => $bodyText]
                )
            ))->render(name: 'email_content', context: $this->templateVariables->toArray());

            $this->setTemplateVariable(key: 'content', value: $mailBody);

            //Render the $mailBody in the content of the main template
            $this->htmlPart = $htmlTemplate->render(
                name: $this->template->parseName(),
                context: $this->templateVariables->toArray()
            );
            $this->textPart = strip_tags(string: $mailBody);
        } catch (Exception $e) {
            $this->htmlPart = $this->textPart = sprintf(
                'Something went wrong with the merge of the body text. Error message: %s',
                $e->getMessage()
            );
        }
    }
}

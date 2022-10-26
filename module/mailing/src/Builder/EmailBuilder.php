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
    protected ?\Mailing\Entity\DistributionList\User $distributionListUser = null;

    protected ?string $subject = null;

    protected ?string $emailCampaign = null;

    protected ArrayCollection $templateVariables;

    protected ?string $textPart = null;

    protected ?string $htmlPart = null;

    protected Template $template;

    protected ?\Mailing\Entity\User $mailingUser = null;

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

        $this->setSender($mailingService->findDefaultSender());
        $this->setTemplate($mailingService->findDefaultTemplate());

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
                        [
                            'sender_email' => $ownerOrLoggedInUser->getEmail(),
                            'sender_name' => $ownerOrLoggedInUser->getDisplayName(),
                        ]
                    );

                    $this->from = new Recipient(
                        $ownerOrLoggedInUser->getDisplayName(),
                        $ownerOrLoggedInUser->getEmail()
                    );
                }

                break;
            case null:
            default:
                $this->setTemplateVariables(
                    [
                        'sender_email' => $sender->getEmail(),
                        'sender_name' => $sender->getSender(),
                    ]
                );

                $this->from = new Recipient($sender->getSender(), $sender->getEmail());
                break;
        }

        return $this;
    }

    public function getMailjetBody(string $identifier): Body
    {
        $messages = [];

        $message = new Email(
            $this->from->toArray(),
            $this->getTo(),
            $this->getCC(),
            $this->getBCC(),
            $this->subject,
            $this->textPart,
            $this->htmlPart,
            $identifier,
            '',
            $this->getReplyTo(),
            'enabled',
            'enabled',
            $this->emailCampaign,
            $this->getAttachments(),
            $this->getInlinedAttachments(),
            $this->getHeaders()
        );
        $messages[] = $message->toArray();

        return new Body($messages);
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
        if (null === $this->replyTo) {
            return null;
        }

        return $this->replyTo->toArray();
    }

    public function setReplyTo(string $replyToName, string $replyToEmail): EmailBuilder
    {
        $this->replyTo = new Recipient($replyToName, $replyToEmail);

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
        $this->setReplyTo($user->parseFullName(), $user->getEmail());

        return $this;
    }

    public function noReplyTo(): void
    {
        $this->replyTo = null;
    }

    public function addAttachment(string $contentType, string $fileName, string $content): void
    {
        $this->attachments[] = new Attachment(
            $contentType,
            $fileName,
            base64_encode($content),
            $content
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
            $startDate,
            $endDate,
            $title,
            $summary,
            $location,
            $organiser
        );
    }

    public function addUserTo(User $user): EmailBuilder
    {
        //As we add the to user in the builder, we automatically extract the user details in the template variables
        //Only extract user details when mailing is personal
        if ($this->personal) {
            $this->setTemplateVariables(
                [
                    'firstname' => $user->getFirstName(),
                    'lastname' => $user->getLastName(),
                    'fullname' => $user->getDisplayName(),
                    'email' => $user->getEmail(),
                ]
            );
        }

        $this->addTo($user->parseFullName(), $user->getEmail());

        return $this;
    }

    public function setTemplateVariables(array $variables): EmailBuilder
    {
        foreach ($variables as $key => $value) {
            $this->setTemplateVariable($key, $value);
        }

        return $this;
    }

    public function setTemplateVariable($key, $value): EmailBuilder
    {
        $this->templateVariables->set($key, $value);

        return $this;
    }

    public function addTo(string $name, string $email): EmailBuilder
    {
        if ($this->personal && count($this->to) > 0) {
            throw new InvalidArgumentException('Impossible to add more recipients to an personal email');
        }

        $to = new Recipient($name, $email);

        if ($to->isValid()) {
            $this->to[] = $to;
        }

        return $this;
    }

    public function addUserCC(User $user): EmailBuilder
    {
        $this->addCC($user->parseFullName(), $user->getEmail());

        return $this;
    }

    public function addCC(string $name, string $email): EmailBuilder
    {
        if ($this->personal) {
            throw new InvalidArgumentException('Impossible to add CC recipients to an personal email');
        }

        $cc = new Recipient($name, $email);

        if ($cc->isValid()) {
            $this->cc[] = $cc;
        }

        return $this;
    }

    public function addUserBCC(User $user): EmailBuilder
    {
        $this->addBCC($user->parseFullName(), $user->getEmail());

        return $this;
    }

    public function addBCC(string $name, string $email): EmailBuilder
    {
        if ($this->personal) {
            throw  new InvalidArgumentException('Impossible to add BCC recipients to an personal email');
        }

        $bcc = new Recipient($name, $email);

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
            throw new InvalidArgumentException('It is not possible to add a deeplink for a non-personal email');
        }
        //Create a target
        $target = $this->deeplinkService->createTargetFromRoute($route);

        $deeplink = $this->deeplinkService->createDeeplink(
            $target,
            $user,
            $key
        );

        $this->setTemplateVariable(
            'deeplink',
            $this->deeplinkService->parseDeeplinkUrl(
                $deeplink,
                LinkDecoration::SHOW_RAW
            )
        );
    }

    abstract public function renderEmail(): void;

    public function renderTwigTemplate(string $template): ?string
    {
        try {
            //Create a second template in which the content of the email is parsed and render the content in
            (new Environment(
                new ArrayLoader(
                    ['rendered_content' => $template]
                )
            ))->render('rendered_content', $this->templateVariables->toArray());

            return null;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function getMessage(): Message
    {
        $message = new Message();
        $message->setFrom($this->from->toAddress());

        $message->setTo($this->getToAsAddressList());
        $message->setCc($this->getCCAsAddressList());
        $message->setBcc($this->getBccAsAddressList());

        if (null !== $this->replyTo) {
            $message->setReplyTo($this->replyTo->toAddress());
        }

        $message->setSubject($this->subject);

        $html = new Part($this->htmlPart);
        $html->setType(Mime\Mime::TYPE_HTML);
        $html->setCharset('utf-8');
        $html->setEncoding(Mime\Mime::ENCODING_QUOTEDPRINTABLE);

        $plain = new Part('TEXT' . $this->textPart);
        $plain->setCharset('utf-8');
        $plain->setType(Mime\Mime::TYPE_TEXT);
        $plain->setEncoding(Mime\Mime::ENCODING_QUOTEDPRINTABLE);

        $content = new Mime\Message();
        $content->setParts(
            [
                $plain,
                $html,
            ]
        );

        if (!$this->hasMultiParts()) {
            $message->setBody($content);
            $contentTypeHeader = $message->getHeaders()->get('Content-Type');
            /** @phpstan-ignore-next-line */
            $contentTypeHeader->setType(Mime\Mime::MULTIPART_ALTERNATIVE);

            return $message;
        }

        $multiParts = [];
        $contentPart = new Part($content->generateMessage());
        $contentPart->setType(Mime\Mime::MULTIPART_ALTERNATIVE);
        $contentPart->setBoundary($content->getMime()->boundary());

        $multiParts[] = $contentPart;
        foreach ($this->attachments as $attachment) {
            $multiParts[] = $attachment->toMimePart();
        }

        foreach ($this->inlinedAttachments as $attachment) {
            $multiParts[] = $attachment->toMimePart(true);
        }

        foreach ($this->invitations as $invitation) {
            $multiParts[] = $invitation->toMimePart();
        }

        $body = new Mime\Message();
        $body->setParts($multiParts);

        $message->setBody($body);

        $contentTypeHeader = $message->getHeaders()->get('Content-Type');
        /** @phpstan-ignore-next-line */
        $contentTypeHeader->setType(Mime\Mime::MULTIPART_RELATED);

        return $message;
    }

    public function getToAsAddressList(): AddressList
    {
        $to = new AddressList();
        foreach ($this->to as $singleTo) {
            $to->add($singleTo->toAddress());
        }

        return $to;
    }

    public function getCCAsAddressList(): AddressList
    {
        $cc = new AddressList();
        foreach ($this->cc as $singleCC) {
            $cc->add($singleCC->toAddress());
        }

        return $cc;
    }

    public function getBCCAsAddressList(): AddressList
    {
        $bcc = new AddressList();
        foreach ($this->bcc as $singleBCC) {
            $bcc->add($singleBCC->toAddress());
        }

        return $bcc;
    }

    public function hasMultiParts(): bool
    {
        return count($this->attachments) > 0 || count($this->invitations) > 0;
    }

    public function hasMailingUser(): bool
    {
        return null !== $this->mailingUser;
    }

    public function getMailingUser(): ?\Mailing\Entity\User
    {
        return $this->mailingUser;
    }

    public function hasdistributionListUser(): bool
    {
        return null !== $this->distributionListUser;
    }

    public function getdistributionListUser(): ?\Mailing\Entity\DistributionList\User
    {
        return $this->distributionListUser;
    }

    protected function renderSubject(string $mailSubject): void
    {
        try {
            //Create a Twig Template on the fly with the template source content
            $subjectTemplate = new Environment(
                new ArrayLoader(
                    ['template_subject' => $this->template->getSubject()]
                )
            );
            //Create a second template in which the content of the email is parsed and render the content in
            $mailSubject = (new Environment(
                new ArrayLoader(
                    ['email_subject' => $mailSubject]
                )
            ))->render('email_subject', $this->templateVariables->toArray());

            $this->setTemplateVariable('subject', $mailSubject);

            //Render the $mailBody in the content of the main template
            $this->subject = $subjectTemplate->render(
                'template_subject',
                $this->templateVariables->toArray()
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
                new ArrayLoader(
                    [$this->template->parseName() => $this->template->parseSourceContent()]
                )
            );

            //Create a second template in which the content of the email is parsed and render the content in
            $mailBody = (new Environment(
                new ArrayLoader(
                    ['email_content' => $bodyText]
                )
            ))->render('email_content', $this->templateVariables->toArray());

            $this->setTemplateVariable('content', $mailBody);

            //Render the $mailBody in the content of the main template
            $this->htmlPart = $htmlTemplate->render(
                $this->template->parseName(),
                $this->templateVariables->toArray()
            );
            $this->textPart = strip_tags($mailBody);
        } catch (Exception $e) {
            $this->htmlPart = $this->textPart = sprintf(
                'Something went wrong with the merge of the body text. Error message: %s',
                $e->getMessage()
            );
        }
    }
}

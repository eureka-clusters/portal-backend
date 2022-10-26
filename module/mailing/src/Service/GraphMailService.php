<?php

declare(strict_types=1);

namespace Mailing\Service;

use Microsoft\Graph\Model\Message;
use Microsoft\Graph\Model\ItemBody;
use Microsoft\Graph\Model\BodyType;
use Microsoft\Graph\Model\EmailAddress;
use Microsoft\Graph\Model\Recipient;
use Microsoft\Graph\Model\FileAttachment;
use Admin\Helper\GetAzureAccessToken;
use GuzzleHttp\Psr7\Stream;
use JetBrains\PhpStorm\Pure;
use Mailing\Builder\EmailBuilder;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Http\GraphResponse;
use Microsoft\Graph\Model;

class GraphMailService
{
    private readonly Graph $graph;

    #[Pure] public function __construct(private readonly GetAzureAccessToken $azureAccessToken)
    {
        $this->graph = new Graph();
    }

    public function testSendMail(EmailBuilder $emailBuilder): GraphResponse
    {
        $azureAccessToken = $this->azureAccessToken;
        $this->graph->setAccessToken($azureAccessToken());

        $message = $this->createEmail($emailBuilder);

        $body = ["message" => $message];

        $sender = $emailBuilder->getSender()->getEmail();

        return $this->graph->createRequest("POST", "/users/" . $sender . "/sendMail")
            ->attachBody($body)
            ->execute();
    }

    public function createEmail(EmailBuilder $emailBuilder): Message
    {
        $message = new Message();
        $message->setSubject($emailBuilder->getSubject());

        $body = new ItemBody();
        $body->setContent($emailBuilder->getHtmlPart());
        $bodyType = new BodyType(BodyType::HTML);

        $body->setContentType($bodyType);
        $message->setBody($body);


        $recipients = [];
        foreach ($emailBuilder->getTo() as $to) {
            $emailAddress = new EmailAddress();
            $emailAddress->setAddress($to['Email']);

            $recipients[] = (new Recipient())->setEmailAddress($emailAddress);
        }
        $message->setToRecipients($recipients);

        $ccRecipients = [];
        foreach ($emailBuilder->getCC() as $cc) {
            $emailAddress = new EmailAddress();
            $emailAddress->setAddress($cc['Email']);

            $ccRecipients[] = (new Recipient())->setEmailAddress($emailAddress);
        }
        if (!empty($ccRecipients)) {
            $message->setCcRecipients($ccRecipients);
        }

        $bccRecipients = [];
        foreach ($emailBuilder->getBCC() as $bcc) {
            $emailAddress = new EmailAddress();
            $emailAddress->setAddress($bcc['Email']);

            $bccRecipients[] = (new Recipient())->setEmailAddress($emailAddress);
        }
        if (!empty($ccRecipients)) {
            $message->setBccRecipients($bccRecipients);
        }

        $attachments = [];
        foreach ($emailBuilder->getAttachments() as $attachment) {
            $fileAttachment = new FileAttachment();
            $fileAttachment->setName($attachment['Filename']);
            $fileAttachment->setODataType("#microsoft.graph.fileAttachment");
            $fileAttachment->setContentType($attachment['ContentType']);
            $fileAttachment->setContentBytes($attachment['Base64Content']);
            $attachments[] = $fileAttachment;
        }

        if (!empty($attachments)) {
            $message->setAttachments($attachments);
        }

        return $message;
    }

}

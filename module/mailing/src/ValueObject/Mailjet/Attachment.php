<?php

declare(strict_types=1);

namespace Mailing\ValueObject\Mailjet;

use Laminas\Mime\Mime;
use Laminas\Mime\Part;

final class Attachment
{
    public function __construct(private readonly string $contentType, private readonly string $fileName, private readonly string $base64Content, private $rawContent = null, private readonly ?string $contentId = null)
    {
    }

    public function toArray(): array
    {
        $return = [
            'ContentType'   => $this->contentType,
            'Filename'      => $this->fileName,
            'Base64Content' => $this->base64Content,
        ];

        if (null !== $this->contentId) {
            $return ['ContentId'] = $this->contentId;
        }
        return $return;
    }

    public function toMimePart(bool $inline = false): Part
    {
        $mimePart = new Part(content: $this->rawContent);
        $mimePart->setType(type: $this->contentType);
        $mimePart->setFileName(fileName: $this->fileName);
        $mimePart->setDisposition(disposition: $inline ? Mime::DISPOSITION_INLINE : Mime::DISPOSITION_ATTACHMENT);
        $mimePart->setEncoding(encoding: Mime::ENCODING_BASE64);

        return $mimePart;
    }
}

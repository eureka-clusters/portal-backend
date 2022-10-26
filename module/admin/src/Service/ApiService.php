<?php

declare(strict_types=1);

namespace Admin\Service;

use Admin\Entity\Api\Log;
use Application\Service\AbstractService;

class ApiService extends AbstractService
{
    public function saveLog(
        int $type,
        string $class,
        string $payload,
        int $statusCode,
        string $status,
        ?string $response
    ): void {
        $log = new Log();
        $log->setClass(class: $class);
        $log->setType(type: $type);
        $log->setPayload(payload: $payload);
        $log->setStatusCode(statusCode: $statusCode);
        $log->setStatus(status: $status);
        $log->setResponse(response: $response);

        $this->save(entity: $log);
    }
}

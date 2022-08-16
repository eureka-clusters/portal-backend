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
        $log->setClass($class);
        $log->setType($type);
        $log->setPayload($payload);
        $log->setStatusCode($statusCode);
        $log->setStatus($status);
        $log->setResponse($response);

        $this->save($log);
    }
}

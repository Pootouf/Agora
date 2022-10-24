<?php

namespace AGORA\Game\Puissance4Bundle\Service;

use Psr\Log\LoggerInterface;

class Puissance4LogService
{
    private $logger;

    public function __construct(LoggerInterface $socketlogger)
    {
        $this->logger = $socketlogger;
    }

    public function logError($msg)
    {
        $this->logger->error($msg);
    }

    public function logInfo($msg)
    {
        $this->logger->info($msg);
    }
}

?>

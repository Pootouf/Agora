<?php


namespace AGORA\Game\SQPBundle\Service;

use Psr\Log\LoggerInterface;

class SqpLogService
{
    private $logger;

    public function __construct(LoggerInterface $socketlogger)
    {
        $this->logger = $socketlogger;
    }

    public function logError($msg){
        $this->logger->error($msg);
    }

    public function logInfo($msg){
        $this->logger->info($msg);
    }

}


?>
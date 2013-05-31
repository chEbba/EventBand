<?php

namespace EventBand\Logger;

use EventBand\Transport\Amqp\Driver\MessagePublication;
use Psr\Log\LoggerInterface;

interface PublicationLogger extends LoggerInterface
{

    public function published(MessagePublication $publication, $exchange, $routingKey);

}

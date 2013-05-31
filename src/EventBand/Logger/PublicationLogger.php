<?php

namespace EventBand\Logger;

use EventBand\Transport\Amqp\Driver\MessagePublication;

interface PublicationLogger
{

    public function published(MessagePublication $publication, $exchange, $routingKey);

}

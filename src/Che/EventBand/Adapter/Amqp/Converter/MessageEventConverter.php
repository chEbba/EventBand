<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace Che\EventBand\Adapter\Amqp\Converter;

use Che\EventBand\Adapter\Amqp\Driver\AmqpMessage;
use Symfony\Component\EventDispatcher\Event;

/**
 * Description of MessageFactory
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
interface MessageEventConverter
{
    /**
     * @param Event $event
     *
     * @return AmqpMessage
     */
    public function eventToMessage(Event $event);

    /**
     * @param AmqpMessage $message
     *
     * @return Event
     */
    public function messageToEvent(AmqpMessage $message);
}

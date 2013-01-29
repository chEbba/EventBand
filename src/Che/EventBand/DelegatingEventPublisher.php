<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace Che\EventBand;

use Symfony\Component\EventDispatcher\Event;

/**
 * Event publisher which delegates publish to internal publishers with loader
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class DelegatingEventPublisher implements EventPublisher
{
    private $loader;

    /**
     * Constructor with publisher loader
     *
     * @param EventPublisherLoader $loader
     */
    public function __construct(EventPublisherLoader $loader)
    {
        $this->loader = $loader;
    }

    /**
     * {@inheritDoc}
     */
    public function publishEvent(Event $event)
    {
        try {
            $publisher = $this->loader->loadPublisherForEvent($event);
        } catch (LoadPublisherException $e) {
            throw new PublishEventException($event, 'Can not find publisher to delegate to', $e);
        }

        $publisher->publishEvent($event);
    }
}

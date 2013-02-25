<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace Che\EventBand\Tests;

use Che\EventBand\Publisher\PublishEventListener;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\EventDispatcher\Event;

/**
 * Test for PublishEventListener
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class PublishEventListenerTest extends TestCase
{
    /**
     * @var \Che\EventBand\Publisher\EventPublisher|\PHPUnit_Framework_MockObject_MockObject
     */
    private $publisher;

    protected function setUp()
    {
        $this->publisher = $this->getMock('Che\EventBand\Publisher\EventPublisher');
    }

    /**
     * @test __invoke publishes event
     */
    public function eventPublish()
    {
        $listener = new PublishEventListener($this->publisher);
        $event = new Event();
        $this->publisher
            ->expects($this->once())
            ->method('publishEvent')
            ->with($event);

        $listener($event);
    }

    /**
     * @test __invoke stop propagation
     */
    public function stopPropagation()
    {
        $listener = new PublishEventListener($this->publisher, false);
        $event = new Event();

        $listener($event);

        $this->assertTrue($event->isPropagationStopped());
    }
}

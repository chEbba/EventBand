<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace EventBand\Tests;

use EventBand\BandDispatcher;
use EventBand\CallbackSubscription;
use EventBand\Event;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Test for CallbackSubscription
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class CallbackSubscriptionTest extends TestCase
{
    /**
     * @test dispatch uses provided callback
     */
    public function dispatchWithCallback()
    {
        $event = $this->getMock('EventBand\Event');
        $dispatcher = $this->getMock('EventBand\BandDispatcher');

        $subscription = new CallbackSubscription(
            'event.name',
            function (Event $providedEvent, BandDispatcher $providedDispatcher) use ($event, $dispatcher) {
                $this->assertSame($event, $providedEvent);
                $this->assertSame($dispatcher, $providedDispatcher);

                return true;
            }
        );

        $this->assertTrue($subscription->dispatch($event, $dispatcher));
    }
}

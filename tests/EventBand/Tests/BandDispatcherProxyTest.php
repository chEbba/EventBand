<?php
/**
 * @LICENSE_TEXT
 */

namespace EventBand\Tests;

use EventBand\BandDispatcherProxy;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class BandDispatcherProxyTest
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 */
class BandDispatcherProxyTest extends TestCase
{
    const EVENT_PREFIX = 'prefix.';
    /**
     * @var BandDispatcherProxy
     */
    private $proxy;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ListenerContainerDispatcher
     */
    private $dispatcher;

    protected function setUp()
    {
        $this->dispatcher = $this->getMockForAbstractClass(__NAMESPACE__ . '\ListenerContainerDispatcher');
        $this->proxy = new BandDispatcherProxy($this->dispatcher, self::EVENT_PREFIX);
    }

    /**
     * @test listener is added to internal dispatcher with prefixed event name and same priority
     */
    public function addPrefixedListener()
    {
        $this->proxy->addListener('event_name', function () {}, 100);

        $eventName = self::EVENT_PREFIX . 'event_name';

        // Has only one event and it is prefixed
        $this->assertEquals([$eventName], $this->dispatcher->getEvents());
        // Has only one listener with specified priority
        $this->assertCount(1, $this->dispatcher->listeners[$eventName]);
        $this->assertArrayHasKey(100, $this->dispatcher->listeners[$eventName]);
    }

    /**
     * @test remove original listener from internal dispatcher with registered event name
     */
    public function removeOriginalListener()
    {
        $listener = function () {};
        $this->proxy->addListener('event_name', $listener);
        $eventName = $this->dispatcher->fistEventKey();

        $this->dispatcher
            ->expects($this->once())
            ->method('removeListener')
            ->with($eventName, $this->dispatcher->listeners[$eventName][0])
        ;

        $this->proxy->removeListener('event_name', $listener);
    }

    /**
     * @test check listeners in internal dispatcher with prefixed name
     */
    public function hasPrefixedListeners()
    {
        $this->dispatcher
            ->expects($this->once())
            ->method('hasListeners')
            ->with(self::EVENT_PREFIX . 'event_name')
            ->will($this->returnValue(true));
        ;

        $this->assertTrue($this->proxy->hasListeners('event_name'));
    }

    /**
     * @test get original event listeners
     */
    public function getOriginalEventListeners()
    {
        $this->proxy->addListener('event_name', $listener1 = function () {}, 0);
        $this->proxy->addListener('event_name', $listener2 = function () {}, 1);

        $eventName = $this->dispatcher->fistEventKey();
        $this->dispatcher
            ->expects($this->once())
            ->method('getListeners')
            ->with($eventName)
            ->will($this->returnValue($this->dispatcher->listeners[$eventName]))
        ;

        $this->assertEquals([$listener1, $listener2], $this->proxy->getListeners('event_name'));
    }

    /**
     * @test get all original listeners
     */
    public function getAllOriginalListeners()
    {
        $this->proxy->addListener('event_name1', $listener1 = function () {});
        $this->proxy->addListener('event_name2', $listener2 = function () {});

        $this->dispatcher
            ->expects($this->once())
            ->method('getListeners')
            ->with(null)
            ->will($this->returnValue($this->dispatcher->listeners))
        ;


        $this->assertEquals(
            [
                'event_name1' => [$listener1],
                'event_name2' => [$listener2]
            ],
            $this->proxy->getListeners()
        );
    }

    /**
     * @test dispatch prefixed event
     */
    public function dispatchPrefixedEvent()
    {
        $this->dispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with(self::EVENT_PREFIX . 'event_name', new Event())
            ->will($this->returnValue($event = new Event()))
        ;

        $this->assertSame($event, $this->proxy->dispatch('event_name', $event));
    }

    /**
     * @test wrapper unprefix event and use internal dispatcher
     */
    public function wrapperUnprefixEvent()
    {
        $called = false;
        $dispatchedEvent = new Event();
        $dispatchedEvent->setDispatcher($this->dispatcher);

        $listener = function (Event $event, $eventName, EventDispatcherInterface $dispatcher) use (&$called, $dispatchedEvent) {
            $called = true;
            $this->assertSame($dispatchedEvent, $event);
            $this->assertEquals('event_name', $eventName);
            $this->assertEquals('event_name', $event->getName());
            $this->assertSame($this->dispatcher, $event->getDispatcher());
            $this->assertSame($this->dispatcher, $dispatcher);
        };

        $this->proxy->addListener('event_name', $listener);

        $eventName = $this->dispatcher->fistEventKey();
        $dispatchedEvent->setName($eventName);
        $wrapper = $this->dispatcher->listeners[$eventName][0];

        call_user_func($wrapper, $dispatchedEvent);
        $this->assertTrue($called);
    }

    // TODO: add subscriber tests
}

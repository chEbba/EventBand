<?php
/**
 * @LICENSE_TEXT
 */

namespace EventBand\Tests;

use Che\ServiceLocator\ServiceLocator;
use EventBand\BandDispatcherLocator;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Class BandDispatcherLocatorTest
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 */
class BandDispatcherLocatorTest extends TestCase
{
    /**
     * @var BandDispatcherLocator
     */
    private $dispatcherLocator;
    /**
     * @var ServiceLocator|\PHPUnit_Framework_MockObject_MockObject
     */
    private $locator;

    protected function setUp()
    {
        $this->locator = $this->getMock('Che\LogStock\Loader\Container\ServiceLocator');
        $this->dispatcherLocator = new BandDispatcherLocator($this->locator);
    }

    /**
     * @test default dispatcher is located at empty name
     */
    public function defaultDispatcherEmptyName()
    {
        $dispatcher = $this->getDispatcherMock();
        $this->locator
            ->expects($this->once())
            ->method('getService')
            ->with('')
            ->will($this->returnValue($dispatcher));

        $this->assertSame($dispatcher, $this->dispatcherLocator->getDefaultDispatcher());
    }

    /**
     * @test no dispatcher with empty name generates exception
     */
    public function noEmptyNameService()
    {
        try {
            $this->dispatcherLocator->getDefaultDispatcher();
        } catch (\Exception $e) {
            // OK
            return;
        }

        $this->fail('No exception was thrown');
    }

    /**
     * @test dispatcher is loaded from service locator by name
     */
    public function dispatcherService()
    {
        $dispatcher = $this->getDispatcherMock();
        $this->locator
            ->expects($this->once())
            ->method('getService')
            ->with('foo')
            ->will($this->returnValue($dispatcher));

        $this->assertSame($dispatcher, $this->dispatcherLocator->getBandDispatcher('foo'));
    }

    /**
     * @test no service was found with name throws exception
     */
    public function noDispatcherIsLoaded()
    {
        try {
            $this->dispatcherLocator->getBandDispatcher('foo');
        } catch (\OutOfBoundsException $e) {
            // OK
            return;
        }

        $this->fail('No exception was thrown');
    }

    /**
     * @test if service was loaded but it is not a dispatcher exception is thrown
     */
    public function serviceIsNotDispatcher()
    {
        $this->locator
            ->expects($this->once())
            ->method('getService')
            ->with('foo')
            ->will($this->returnValue(new \stdClass()));

        try {
            $this->dispatcherLocator->getBandDispatcher('foo');
        } catch (\OutOfBoundsException $e) {
            // OK
            return;
        }

        $this->fail('No exception was thrown');
    }

    /**
     * @test exception is thrown if band is empty
     */
    public function emptyBandName()
    {
        try {
            $this->dispatcherLocator->getBandDispatcher('');
        } catch (\InvalidArgumentException $e) {
            // OK
            return;
        }

        $this->fail('No exception was thrown');
    }

    private function getDispatcherMock()
    {
        return $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
    }
}

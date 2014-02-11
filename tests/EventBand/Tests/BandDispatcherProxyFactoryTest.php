<?php
/**
 * @LICENSE_TEXT
 */

namespace EventBand\Tests;

use EventBand\BandDispatcherProxyFactory;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Class BandDispatcherProxyFactoryTest
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 */
class BandDispatcherProxyFactoryTest extends TestCase
{
    private $dispatcher;
    /**
     * @var BandDispatcherProxyFactory
     */
    private $factory;

    protected function setUp()
    {
        $this->dispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $this->factory = new BandDispatcherProxyFactory($this->dispatcher);
    }


    /**
     * @test dispatcher factory creates proxy dispatchers
     */
    public function proxyDispatcher()
    {
        $bandDispatcher = $this->factory->getBandDispatcher('foo');
        $this->assertInstanceOf('EventBand\BandDispatcherProxy', $bandDispatcher);
        $this->assertSame($this->dispatcher, $bandDispatcher->getInternalDispatcher());
        $this->assertContains('foo', $bandDispatcher->getEventPrefix());
    }

    /**
     * @test empty band exception
     */
    public function emptyBand()
    {
        try {
            $this->factory->getBandDispatcher('');
        } catch (\Exception $e) {
            // OK
            return;
        }

        $this->fail('Exception was not thrown');
    }
}

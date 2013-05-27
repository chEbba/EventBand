<?php
/**
 * @LICENSE_TEXT
 */

namespace EventBand\Tests\Transport\Amqp;

use EventBand\Transport\DelegatingTransportConfigurator;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * DelegatingTransportConfigurator test
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 */
class DelegatingTransportConfiguratorTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $internalConfigurator;
    /**
     * @var DelegatingTransportConfigurator
     */
    private $configurator;

    /**
     * Setup configurator
     */
    protected function setUp()
    {
        $this->internalConfigurator = $this->getMock('EventBand\Transport\TransportConfigurator');
        $this->internalConfigurator
            ->expects($this->any())
            ->method('supportsDefinition')
            ->will($this->returnCallback(function ($definition) {
                return $definition === 'supported';
            }))
        ;
        $this->configurator = new DelegatingTransportConfigurator([$this->internalConfigurator]);
    }

    /**
     * @test supportsDefinition delegates check to internal configurator
     */
    public function delegatingSupport()
    {
        $this->assertTrue($this->configurator->supportsDefinition('supported'));
        $this->assertFalse($this->configurator->supportsDefinition('notSupported'));
    }

    /**
     * @test setUpDefinition delegates setup to internal definition
     */
    public function delegatingSetup()
    {
        $this->internalConfigurator
            ->expects($this->once())
            ->method('setUpDefinition')
        ;

        $this->configurator->setUpDefinition('supported');
    }

    /**
     * @test setUpDefinition setups all supported configurators
     */
    public function allSupportedConfiguratorsAreUsed()
    {
        $this->internalConfigurator
            ->expects($this->exactly(2))
            ->method('setUpDefinition')
        ;

        $this->configurator->registerConfigurator('test2', $this->internalConfigurator);
        $this->configurator->setUpDefinition('supported');
    }
}

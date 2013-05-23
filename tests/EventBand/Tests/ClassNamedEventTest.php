<?php
/**
 * @LICENSE_TEXT
 */

namespace EventBand\Tests;

use EventBand\ClassNamedEvent;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Class ClassNamedEventTest
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 */
class ClassNamedEventTest extends TestCase
{
    /**
     * @test event name is base on class: dot-separated underscored lowercase name
     */
    public function classBasedName()
    {
        $this->assertEquals('event_band.tests.named_stub', NamedStubEvent::name());
    }
}

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
     * @dataProvider classEventNames
     *
     * @param string $className
     * @param string $eventName
     */
    public function classBasedName($className, $eventName)
    {
        $this->assertEquals($eventName, ClassNamedEvent::generateName($className));
    }

    /**
     * @return array An array of [[$className, $eventName], ...]
     */
    public function classEventNames()
    {
        return [
            ['Foo\Ns\Class', 'foo.ns.class'],
            ['FooBar\Ns\Class', 'foo_bar.ns.class'],
            ['FooBar\Ns\ClassName', 'foo_bar.ns.class_name'],
            ['FooBar\Ns\Class35Name', 'foo_bar.ns.class35_name'],
            ['FooBar\Ns\ClassNameEvent', 'foo_bar.ns.class_name'],
        ];
    }
}

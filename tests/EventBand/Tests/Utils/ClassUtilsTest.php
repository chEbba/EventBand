<?php
/**
 * @LICENSE_TEXT
 */

namespace EventBand\Tests\Utils;

use EventBand\Utils\ClassUtils;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Class ClassUtilsTest
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 */
class ClassUtilsTest extends TestCase
{
    /**
     * @test class to name conversion
     * @dataProvider classNames
     *
     * @param string $className
     * @param string $convertedName
     */
    public function classToNameConversion($className, $convertedName)
    {
        $this->assertEquals($convertedName, ClassUtils::classToName($className, '/', '-'));
    }

    /**
     * @test name to class conversion
     * @dataProvider classNames
     *
     * @param string $className
     * @param string $convertedName
     */
    public function nameToClassConversion($className, $convertedName)
    {
        $this->assertEquals($className, ClassUtils::nameToClass($convertedName, '/', '-'));
    }

    /**
     * @return array An array of [[$className, $convertedName], ...]
     */
    public function classNames()
    {
        return [
            ['Foo\Ns\Class', 'foo/ns/class'],
            ['FooBar\Ns\Class', 'foo-bar/ns/class'],
            ['FooBar\Ns\ClassName', 'foo-bar/ns/class-name'],
            ['FooBar\Ns\Class35Name', 'foo-bar/ns/class35-name']
        ];
    }
}

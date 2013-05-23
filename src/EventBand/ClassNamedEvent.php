<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace EventBand;

use EventBand\Utils\ClassUtils;

/**
 * Base event class with class-based name
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
abstract class ClassNamedEvent implements Event
{
    const IGNORED_SUFFIX = 'event';

    private static $names;

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return self::name();
    }

    /**
     * Get class event name
     *
     * @return string
     */
    public static function name()
    {
        $class = get_called_class();
        if (!isset(self::$names[$class])) {
            self::$names[$class] = self::generateName($class);
        }

        return self::$names[$class];
    }

    /**
     * Generate event name based on class
     *
     * @param string $class
     *
     * @return string
     */
    public static function generateName($class)
    {
        $name = ClassUtils::classToName($class, '.', '_', self::IGNORED_SUFFIX);

        // Remove suffix
        if (substr($name, -1 * strlen(self::IGNORED_SUFFIX)) === self::IGNORED_SUFFIX) {
            $name = substr($name, 0, -1 * strlen(self::IGNORED_SUFFIX));
            $name = rtrim($name, '._');
        }

        return $name;
    }
}

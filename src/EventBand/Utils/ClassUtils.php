<?php
/**
 * @LICENSE_TEXT
 */

namespace EventBand\Utils;

/**
 * Class ClassUtils
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 */
class ClassUtils
{
    /**
     * Convert class name to safe representation by breaking words and replacing ns separator.
     * Ex.
     *  $class          'VendorPackage\\Foo\\BarEvent'
     *  $nsSeparator    '.'
     *  $wordSeparator  '_'
     *  $suffix         'event'
     *  result:         vendor_package.foo.bar
     *
     * @param string $class
     * @param string $nsSeparator
     * @param string $wordSeparator
     * @param string $suffix
     *
     * @return string Converted name
     */
    public static function classToName($class, $nsSeparator = '.', $wordSeparator = '_', $suffix = '')
    {
        // TODO: parameter validation and conversion

        $replacePattern = sprintf("\\1%s\\2", $wordSeparator);
        $name = strtolower(preg_replace(
            ['/([A-Z]+)([A-Z][a-z])/', '/([a-z\d])([A-Z])/'],
            [$replacePattern, $replacePattern],
            strtr($class, '\\', $nsSeparator)
        ));

        // Remove suffix if set and exists in string
        if (!empty($suffix) && substr($name, -1 * strlen($suffix)) === $suffix) {
            $name = substr($name, 0, -1 * strlen($suffix));
            $name = rtrim($name, "{$nsSeparator}{$wordSeparator}");
        }

        return $name;
    }
}
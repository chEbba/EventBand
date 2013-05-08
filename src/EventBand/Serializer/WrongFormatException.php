<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace EventBand\Serializer;

/**
 * Format of deserialized string is wrong
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class WrongFormatException extends SerializerException
{
    private $format;

    /**
     * Constructor from format
     *
     * @param string          $format   Expected format
     * @param string          $reason   Error reason
     * @param \Exception|null $previous Previous exception
     */
    public function __construct($format, $reason, \Exception $previous = null)
    {
        $this->format = $format;

        parent::__construct(sprintf('Wrong data format for "%s" deserialization: %s', $format, $reason), $previous);
    }

    /**
     * Get expected format
     *
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }
}

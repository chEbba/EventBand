<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace Che\EventBand\Reader;

/**
 * Exception while loading subscriber
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class ReaderLoadException extends \RuntimeException
{
    /**
     * @param string          $name     Subscriber name
     * @param string          $reason   Error reason
     * @param \Exception|null $previous Previous exception
     */
    public function __construct($name, $reason, \Exception $previous = null)
    {
        parent::__construct(sprintf('Reader "%s" can not be loaded: %s', $name, $reason), 0, $previous);
    }
}

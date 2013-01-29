<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace Che\EventBand;

/**
 * Exception while loading processor
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class LoadProcessorException extends LoadException
{
    private $processorName;

    /**
     * @param string          $name     Processor name
     * @param string          $reason   Error reason
     * @param \Exception|null $previous Previous exception
     */
    public function __construct($name, $reason, \Exception $previous = null)
    {
        $this->processorName = $name;

        parent::__construct(sprintf('Processor "%s" can not be loaded: %s', $name, $reason), 0, $previous);
    }

    /**
     * Get processor name
     *
     * @return string
     */
    public function getProcessorName()
    {
        return $this->processorName;
    }
}

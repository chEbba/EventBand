<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace Che\EventBand\Adapter;

/**
 * Runtime exception for wrong configuration parameter
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class InvalidParameterException extends \RuntimeException
{
    private $parameter;

    /**
     * Constructor
     *
     * @param string          $parameter Parameter name
     * @param string          $reason    Error reason
     * @param \Exception|null $previous  Previous exception
     */
    public function __construct($parameter, $reason, \Exception $previous = null)
    {
        $this->parameter = $parameter;

        parent::__construct(sprintf('Parameter "%s" is invalid: %s', $parameter, $reason), 0, $previous);
    }

    /**
     * Get invalid parameter
     *
     * @return string
     */
    public function getParameter()
    {
        return $this->parameter;
    }
}

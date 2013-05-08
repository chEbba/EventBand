<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace EventBand\Processor;

use EventBand\ClassNamedEvent;

/**
 * Description of DispatchFinishEvent
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
abstract class StoppableDispatchEvent extends ClassNamedEvent
{
    private $dispatching = true;

    public function isDispatching()
    {
        return $this->dispatching;
    }

    public function stopDispatching()
    {
        $this->dispatching = false;
    }
}

<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace EventBand\Processor;

/**
 * Description of DispatchFinishEvent
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
trait StoppableProcess
{
    private $processing = true;

    public function isProcessing()
    {
        return $this->processing;
    }

    public function stopProcessing()
    {
        $this->processing = false;
    }
}

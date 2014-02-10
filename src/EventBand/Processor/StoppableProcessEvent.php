<?php
/**
 * @LICENSE_TEXT
 */

namespace EventBand\Processor;

/**
 * Class StoppableProcessEvent
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 */
interface StoppableProcessEvent
{
    public function isProcessing();
    public function stopProcessing();
} 

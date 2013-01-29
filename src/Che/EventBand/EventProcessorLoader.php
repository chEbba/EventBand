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
 * Loader for event processors
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
interface EventProcessorLoader
{
    /**
     * Load processor by name
     *
     * @param string $name
     *
     * @return callback Signature: void function(Event $event)
     * @throws LoadProcessorException
     */
    public function loadProcessor($name);
}

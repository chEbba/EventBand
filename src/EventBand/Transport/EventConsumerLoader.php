<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace EventBand\Transport;

/**
 * Load reader by name
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
interface EventConsumerLoader
{
    /**
     * Load event reader
     *
     * @param string $name
     *
     * @return EventConsumer
     * @throws ConsumerLoadException
     */
    public function loadConsumer($name);
}

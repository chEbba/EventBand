<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace Che\EventBand\Adapter\Amqp\Definition;

/**
 * Description of QueueBuilder
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class QueueBuilder extends ModelBuilder implements QueueDefinition
{
    private $exclusive;

    /**
     * @return $this Provides the fluent interface
     */
    public function exclusive()
    {
        $this->exclusive = true;

        return $this;
    }

    public function isExclusive()
    {
        return $this->exclusive;
    }
}

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
 * Description of ModelDefinition
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
abstract class ModelBuilder
{
    private $builder;
    private $name;
    private $durable = true;
    private $autoDeleted = false;
    private $bindings = array();

    public function __construct(AmqpBuilder $builder,$name)
    {
        $this->builder = $builder;
        $this->name = $name;
    }

    /**
     * @return AmqpBuilder
     */
    public function end()
    {
        return $this->builder;
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * @return $this Provides the fluent interface
     */
    public function transient()
    {
        $this->durable = false;

        return $this;
    }

    public function isDurable()
    {
        return $this->durable;
    }

    /**
     * @return $this Provides the fluent interface
     */
    public function autoDelete()
    {
        $this->autoDeleted = true;

        return $this;
    }

    public function isAutoDeleted()
    {
        return $this->autoDeleted;
    }

    /**
     * @param string $source
     * @param string $routingKey
     *
     * @return $this Provides the fluent interface
     */
    public function bind($source, $routingKey = '')
    {
        $this->bindings[$source][] = $routingKey;

        return $this;
    }

    public function getBindings()
    {
        return $this->bindings;
    }

}

<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace EventBand\Transport\Amqp\Definition;

/**
 * Base definition builder for queues and exchanges
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
abstract class ModelBuilder implements \JsonSerializable
{
    private $builder;
    private $name;
    private $durable = true;
    private $autoDeleted = false;
    private $bindings = [];

    public function __construct(AmqpBuilder $builder, $name)
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

    public function options(array $options)
    {
        foreach ($options as $key => $value) {
            switch ($key) {
                case 'bind':
                    foreach ($value as $source => $routingKeys) {
                        foreach ($routingKeys as $routingKey) {
                            $this->bind($source, $routingKey);
                        }
                    }
                    break;

                default:
                    if ($value) {
                        $this->$key($value);
                    }
            }

        }

        return $this;
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

    /**
     * Converts properties to serializable array
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'name' => $this->name,
            'durable' => $this->durable,
            'autoDeleted' => $this->autoDelete(),
            'bindings' => $this->bindings
        ];
    }
}

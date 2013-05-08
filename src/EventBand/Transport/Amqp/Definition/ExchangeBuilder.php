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
 * Description of ExchangeBuilder
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class ExchangeBuilder extends ModelBuilder implements ExchangeDefinition
{
    private $type;
    private $internal = false;

    public function __construct(AmqpBuilder $builder, $name, $type = ExchangeDefinition::TYPE_DIRECT)
    {
        if (!in_array($type, self::getTypes())) {
            throw new \InvalidArgumentException(sprintf(
                'Unknown type "%s" (expected one of: "%s")',
                $type, implode('", "', self::getTypes())
            ));
        }
        $this->type = $type;

        parent::__construct($builder, $name);
    }

    final public static function getTypes()
    {
        return array(
            ExchangeDefinition::TYPE_DIRECT,
            ExchangeDefinition::TYPE_FANOUT,
            ExchangeDefinition::TYPE_HEADER,
            ExchangeDefinition::TYPE_TOPIC
        );
    }

    public function getType()
    {
        return $this->type;
    }

    /**
     * @return $this Provides the fluent interface
     */
    public function internal()
    {
        $this->internal = true;

        return $this;
    }

    public function isInternal()
    {
        return $this->internal;
    }
}

<?php

namespace Che\EventBand\Adapter\AmqpLib;

/**
 * Description of QueueOptions
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 */
class ExchangeOptions
{
    const TYPE_DIRECT = 'direct';
    const TYPE_TOPIC = 'topic';
    const TYPE_FANOUT = 'fanout';
    const TYPE_HEADER = 'header';

    private $name;
    private $type;
    private $passive = false;
    private $durable = true;
    private $autoDeleted = false;

    public function __construct($name, $type = self::TYPE_DIRECT, $passive = false, $durable = true, $autoDeleted = false)
    {
        $this->name = $name;
        if (!in_array($type, self::getTypes())) {
            throw new \InvalidArgumentException(
                sprintf('Unknown type "%s". Valid types: (%s)', $type, implode(', ', self::getTypes()))
            );
        }
        $this->type = $type;
        $this->passive = $passive;
        $this->durable = $durable;
        $this->autoDeleted = $autoDeleted;
    }

    public static function getTypes()
    {
        return array(
            self::TYPE_DIRECT,
            self::TYPE_TOPIC,
            self::TYPE_FANOUT,
            self::TYPE_HEADER
        );
    }

    public function getName()
    {
        return $this->name;
    }

    public function getType()
    {
        return $this->type;
    }

    public function isPassive()
    {
        return $this->passive;
    }

    public function isDurable()
    {
        return $this->durable;
    }

    public function isAutoDeleted()
    {
        return $this->autoDeleted;
    }
}

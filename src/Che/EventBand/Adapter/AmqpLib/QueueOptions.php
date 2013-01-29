<?php

namespace Che\EventBand\Adapter\AmqpLib;

/**
 * Description of QueueOptions
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 */
class QueueOptions
{
    private $name;
    private $exchange;
    private $routingKey;
    private $passive = false;
    private $durable = true;
    private $exclusive = false;
    private $autoDeleted = false;

    public function __construct($name, $exchange, $routingKey = '', $passive = false, $durable = true,
                                $exclusive = false, $autoDeleted = false)
    {
        $this->name = $name;
        $this->exchange = $exchange;
        $this->routingKey = $routingKey;
        $this->passive = $passive;
        $this->durable = $durable;
        $this->exclusive = $exclusive;
        $this->autoDeleted = $autoDeleted;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getExchange()
    {
        return $this->exchange;
    }

    public function getRoutingKey()
    {
        return $this->routingKey;
    }

    public function isPassive()
    {
        return $this->passive;
    }

    public function isDurable()
    {
        return $this->durable;
    }

    public function isExclusive()
    {
        return $this->exclusive;
    }

    public function isAutoDeleted()
    {
        return $this->autoDeleted;
    }

//    private function createChannel()
//    {
//        $ch = $this->getConnection()->channel();
//
//        /** @var $options ExchangeOptions */
//        foreach ($this->exchangeDeclarations as $options) {
//            $ch->exchange_declare(
//                $options->getName(),
//                self::convertType($options->getType()),
//                $options->isPassive(),
//                $options->isDurable(),
//                $options->isAutoDeleted()
//            );
//        }
//
//        /** @var $options QueueOptions */
//        foreach ($this->queueDeclarations as $options) {
//            $ch->queue_declare(
//                $options->getName(),
//                $options->isPassive(),
//                $options->isDurable(),
//                $options->isExclusive(),
//                $options->isAutoDeleted()
//            );
//            $ch->queue_bind($options->getName(), $options->getExchange(), $options->getRoutingKey());
//        }
//
//        return $ch;
//    }
//    private static function convertType($optionType)
//    {
//        $types = array(
//            ExchangeOptions::TYPE_DIRECT => 'direct',
//            ExchangeOptions::TYPE_TOPIC => 'topic',
//            ExchangeOptions::TYPE_FANOUT => 'fanout',
//            ExchangeOptions::TYPE_HEADER => 'header'
//        );
//
//        return $types[$optionType];
//    }
}

<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace Che\EventBand\Serializer\Jms;

use JMS\Serializer\Exception\RuntimeException;
use JMS\Serializer\GenericDeserializationVisitor;
use JMS\Serializer\GenericSerializationVisitor;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\XmlDeserializationVisitor;
use JMS\Serializer\XmlSerializationVisitor;
use Symfony\Component\EventDispatcher\Event;

/**
 * Custom serialization handler for EventWrapper stores an event class name
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class EventWrapperHandler implements SubscribingHandlerInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getSubscribingMethods()
    {
        $formats = array('json', 'xml');
        $type = __NAMESPACE__ . '\EventWrapper';

        $subscriptions = array();
        foreach ($formats as $format) {
            $subscriptions[] = array(
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'type' => $type,
                'method' => 'serializeWrapper' . ucfirst($format),
                'format' => $format
            );
            $subscriptions[] = array(
                'direction' => GraphNavigator::DIRECTION_DESERIALIZATION,
                'type' => $type,
                'method' => 'deserializeWrapper' . ucfirst($format),
                'format' => $format
            );
        }

        return $subscriptions;
    }

    /**
     * Normalize wrapper to serialize with json
     * Event class name is added
     *
     * @param JsonSerializationVisitor $visitor
     * @param EventWrapper             $wrapper
     * @param array                    $type
     *
     * @return array
     */
    public function serializeWrapperJson(JsonSerializationVisitor $visitor, EventWrapper $wrapper, array $type)
    {
        return $this->serializeWrapper($visitor, $wrapper);
    }

    /**
     * Create wrapper from decoded json
     *
     * @param JsonDeserializationVisitor $visitor
     * @param mixed                      $data
     * @param array                      $type
     *
     * @return EventWrapper
     * @throws RuntimeException
     */
    public function deserializeWrapperJson(JsonDeserializationVisitor $visitor, $data, array $type)
    {
        if (!is_array($data)) {
            throw new RuntimeException(sprintf('Deserialize data array expected, got "%s"', gettype($data)));
        }

        return $this->deserializeWrapper($visitor, $data);
    }

    /**
     * Normalize wrapper to serialize with xml
     * Event class name is added
     *
     * @param XmlSerializationVisitor $visitor
     * @param EventWrapper            $wrapper
     * @param array                   $type
     */
    public function serializeWrapperXml(XmlSerializationVisitor $visitor, EventWrapper $wrapper, array $type)
    {
        if (!$visitor->getDocument()) {
            $visitor->visitArray(array(), array('name' => 'array'));
        }
        $element = $visitor->getDocument()->createElement('event');
        $event = $wrapper->getEvent();

        $element->setAttribute('type', get_class($event));

        $visitor->getCurrentNode()->appendChild($element);
        $visitor->setCurrentNode($element);
        $visitor->getNavigator()->accept($wrapper->getEvent(), null, $visitor);
        $visitor->revertCurrentNode();
    }

    /**
     * Create wrapper from decoded xml
     *
     * @param XmlDeserializationVisitor $visitor
     * @param mixed                     $data
     * @param array                     $type
     *
     * @return EventWrapper
     * @throws RuntimeException
     */
    public function deserializeWrapperXml(XmlDeserializationVisitor $visitor, $data, array $type)
    {
        if (!is_object($data)) {
            throw new RuntimeException(sprintf('Deserialized SimpleXMLElement data expected, got "%s"', gettype($data)));
        }
        if (!$data instanceof \SimpleXMLElement) {
            throw new RuntimeException(sprintf('Deserialized SimpleXMLElement data expected, got "%s"', get_class($data)));
        }
        if (!isset($data->event) || !$data->event instanceof \SimpleXMLElement) {
            throw new RuntimeException(sprintf('Deserialized data child node "event" missed'));
        }
        if (!isset($data->event['type'])) {
            throw new RuntimeException(sprintf('Deserialized "type" attribute for d"event" node missed'));
        }

        return new EventWrapper($visitor->getNavigator()->accept($data->event, array('name' => (string) $data->event['type']), $visitor));
    }

    /**
     * Normalize wrapper to serialize with generic serialization
     * Event class name is added
     *
     * @param GenericSerializationVisitor $visitor
     * @param EventWrapper                $wrapper
     *
     * @return array
     */
    protected function serializeWrapper(GenericSerializationVisitor $visitor, EventWrapper $wrapper)
    {
        $setRoot = $visitor->getRoot() === null;
        $data = array(
            'type' => get_class($wrapper->getEvent()),
            'event' => $visitor->getNavigator()->accept($wrapper->getEvent(), null, $visitor)
        );

        if ($setRoot) {
            $visitor->setRoot($data);
        }

        return $data;
    }

    /**
     * Create wrapper from generic decoded array
     *
     * @param GenericDeserializationVisitor $visitor
     * @param array                         $data
     *
     * @return EventWrapper
     * @throws RuntimeException
     */
    protected function deserializeWrapper(GenericDeserializationVisitor $visitor, array $data)
    {
        if (!isset($data['event']) || !isset($data['type'])) {
            throw new RuntimeException('Missed properties in the deserialized data array: "event", "type"');
        }

        $wrapper = new EventWrapper(new Event());
        if ($visitor->getCurrentObject() === null) {
            $visitor->setCurrentObject($wrapper);
        }

        $event = $visitor->getNavigator()->accept($data['event'], array('name' => $data['type']), $visitor);

        if (!is_object($event)) {
            throw new RuntimeException(sprintf('Deserialized Event object expected, got "%s"', gettype($event)));
        }
        if (!$event instanceof Event) {
            throw new RuntimeException(sprintf('Deserialized Event object expected, got "%s"', get_class($event)));
        }

        return new EventWrapper($event);
    }
}

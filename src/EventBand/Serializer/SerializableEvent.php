<?php
/**
 * @LICENSE_TEXT
 */

namespace EventBand\Serializer;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class SeriazlizableEvent
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 */
class SerializableEvent extends Event implements \Serializable
{
    /**
     * {@inheritDoc}
     */
    public function serialize()
    {
        return serialize($this->toSerializableArray());
    }

    /**
     * {@inheritDoc}
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);
        if (!is_array($data)) {
            throw new \RuntimeException(sprintf('Unserialized data is not an array but "%s"', $data));
        }

        $this->fromUnserializedArray($data);
    }

    protected function toSerializableArray()
    {
        return [];
    }

    protected function fromUnserializedArray(array $data)
    {
    }
}

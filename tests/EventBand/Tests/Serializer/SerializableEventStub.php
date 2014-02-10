<?php
/**
 * @LICENSE_TEXT
 */

namespace EventBand\Tests\Serializer;

use EventBand\Serializer\SerializableEvent;

/**
 * Class SerializeEventStub
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 */
class SerializableEventStub extends SerializableEvent
{
    private $foo;

    public function __construct($name, $foo)
    {
        $this->setName($name);
        $this->foo = $foo;
    }

    public function getFoo()
    {
        return $this->foo;
    }

    protected function toSerializableArray()
    {
        return array_merge(parent::toSerializableArray(), ['foo' => $this->foo]);
    }

    protected function fromUnserializedArray(array $data)
    {
        parent::fromUnserializedArray($data);

        if (!isset($data['foo'])) {
            throw new \RuntimeException('Key "foo" is not set in unserialized array');
        }
        $this->foo = $data['foo'];
    }
}

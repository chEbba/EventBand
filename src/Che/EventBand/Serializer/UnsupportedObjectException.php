<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace Che\EventBand\Serializer;

/**
 * Object can not be serialized
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class UnsupportedObjectException extends SerializerException
{
    private $object;

    /**
     * Constructor
     *
     * @param object          $object   Serialized object
     * @param string          $reason   Error reason
     * @param \Exception|null $previous Previous exception
     */
    public function __construct($object, $reason, \Exception $previous)
    {
        $this->object = $object;

        parent::__construct(sprintf('Object "%s" is unsupported by serializer: %s', $this->getObjectAsString(), $reason), $previous);
    }

    /**
     * Get serialized object
     *
     * @return object
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * Get object as string
     *
     * @return string
     */
    public function getObjectAsString()
    {
        if (method_exists($this->object, '__toString')) {
            return $this->object->__toString();
        }

        return sprintf('object[%s]#%s', get_class($this->object), spl_object_hash($this->object));
    }
}

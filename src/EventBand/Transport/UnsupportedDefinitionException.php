<?php
/**
 * @LICENSE_TEXT
 */

namespace EventBand\Transport;

/**
 * Definition is not supported by configurator
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 */
class UnsupportedDefinitionException extends ConfiguratorException
{
    private $definition;

    /**
     * @param mixed           $definition
     * @param string          $reason
     * @param \Exception|null $previous
     */
    public function __construct($definition, $reason, \Exception $previous = null)
    {
        parent::__construct(sprintf('Unsupported definition: %s', $reason), 0, $previous);
    }

    public function getDefinition()
    {
        return $this->definition;
    }
}
<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace EventBand\Routing;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\PropertyAccess\Exception\RuntimeException;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * Event router based on patterns with property placeholders
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class EventPatternRouter implements EventRouter
{
    const SIMPLE_NAME_PATTERN = '{name}';
    const PLACEHOLDER_REGEX = '/\{([a-z0-9_\.\[\]]+)\}/i';

    private $pattern;
    private $placeholders;
    private $accessor;

    public function __construct($pattern = self::SIMPLE_NAME_PATTERN)
    {
        $this->pattern = $pattern;

        $this->accessor = new PropertyAccessor();
    }

    public function getPattern()
    {
        return $this->pattern;
    }

    public function getPlaceholders()
    {
        if ($this->placeholders === null) {
            if (preg_match_all(self::PLACEHOLDER_REGEX, $this->pattern, $matches)) {
                $this->placeholders = array_unique(array_combine($matches[0], $matches[1]));
            } else {
                $this->placeholders = array();
            }
        }

        return $this->placeholders;
    }

    /**
     * {@inheritDoc}
     */
    public function routeEvent(Event $event)
    {
        $replaces = array();
        foreach ($this->getPlaceholders() as $placeholder => $property) {
            try {
                $value = $this->accessor->getValue($event, $property);
            } catch (RuntimeException $e) {
                throw new EventRoutingException($event, sprintf('Can not get value for "%s" property', $placeholder), $e);
            }
            if (!is_scalar($value)) {
                throw new EventRoutingException($event, sprintf('Value for "%s" property is not a scalar', $placeholder));
            }
            $replaces[$placeholder] = $value;
        }

        if (!$replaces) {
            return $this->pattern;
        }

        return strtr($this->pattern, $replaces);
    }
}

<?php
/**
 * @LICENSE_TEXT
 */

namespace EventBand\Transport;

/**
 * Class TransportConfigurator
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 */
interface TransportConfigurator
{
    public function supportsDefinition($definition);
    public function setUpDefinition($definition);
}
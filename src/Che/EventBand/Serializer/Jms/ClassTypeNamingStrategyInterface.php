<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace Che\EventBand\Serializer\Jms;

/**
 * Description of ObjectTypeNamingStrategyInterface
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 */
interface ClassTypeNamingStrategyInterface
{
    public function classToType($class);
    public function typeToClass($type);
}

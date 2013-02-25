<?php
/*
 * Copyright (c)
 * Kirill chEbba Chebunin <iam@chebba.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.
 */

namespace Che\EventBand\Reader;

/**
 * Load reader by name
 *
 * @author Kirill chEbba Chebunin <iam@chebba.org>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
interface EventReaderLoader
{
    /**
     * Load event reader
     *
     * @param string $name
     *
     * @return EventReader
     * @throws ReaderLoadException
     */
    public function loadReader($name);
}

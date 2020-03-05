<?php
/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace Ulrack\Vfs\Common;

use Iterator;
use ArrayAccess;

/**
 * An interface for an iterable file.
 */
interface FileInterface extends Iterator, ArrayAccess
{
    /**
     * Reads the file in chunks of set bytes.
     */
    const MODE_CHUNK = 'chunk';

    /**
     * Reads the file per line.
     */
    const MODE_LINE  = 'line';

    /**
     * Closes the file.
     *
     * @return void
     */
    public function close(): void;
}

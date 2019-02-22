<?php
/**
 * Copyright (C) Jyxon, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace Ulrack\Vfs\Exception;

/**
 * An exception thrown when a file is locked.
 */
class LockedOutException extends FileException
{
    public function __construct(string $filename)
    {
        parent::__construct('File locked', $filename);
    }
}

<?php
/**
 * Copyright (C) Jyxon, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace Ulrack\Vfs\Common;

/**
 * An interface for a file system driver.
 */
interface FileSystemDriverInterface
{
    /**
     * Connects to the file system.
     *
     * @return FileSystemInterface
     */
    public function connect(string $path): FileSystemInterface;

    /**
     * Disconnects from the file system.
     *
     * @return void
     */
    public function disconnect(FilesystemInterface $filesystem): void;
}

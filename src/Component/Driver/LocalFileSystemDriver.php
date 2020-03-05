<?php
/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace Ulrack\Vfs\Component\Driver;

use Ulrack\Vfs\Component\FileSystem\LocalFileSystem;
use Ulrack\Vfs\Common\FileSystemDriverInterface;
use Ulrack\Vfs\Common\FileSystemInterface;
use Ulrack\Vfs\Exception\FileNotFoundException;

/**
 * An implementation of the FileSystemDriverInterface for a local file system.
 */
class LocalFileSystemDriver implements FileSystemDriverInterface
{
    /**
     * Connects to the file system.
     *
     * @return FileSystemInterface
     *
     * @throws FileNotFoundException When the path can not be resolved.
     */
    public function connect(string $path): FileSystemInterface
    {
        $absolutePath = realpath($path);
        if ($absolutePath) {
            return new LocalFileSystem($absolutePath);
        }

        throw new FileNotFoundException($path);
    }

    /**
     * Disconnects from the file system.
     *
     * @return void
     */
    public function disconnect(FilesystemInterface $filesystem): void
    {
        // Explicit disconnect is not required.
        return;
    }
}

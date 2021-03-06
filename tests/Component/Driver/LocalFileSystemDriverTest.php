<?php
/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace Ulrack\Vfs\Tests\Component\Driver;

use Ulrack\Vfs\Exception\FileNotFoundException;
use Ulrack\Vfs\Component\FileSystem\LocalFileSystem;
use Ulrack\Vfs\Component\Driver\LocalFileSystemDriver;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Ulrack\Vfs\Component\Driver\LocalFileSystemDriver
 * @covers \Ulrack\Vfs\Exception\FileNotFoundException
 * @covers \Ulrack\Vfs\Exception\FileException
 */
class LocalFileSystemDriverTest extends TestCase
{
    /**
     * @return void
     *
     * @covers ::connect
     * @covers ::disconnect
     */
    public function testConnection(): void
    {
        $subject = new LocalFileSystemDriver();
        $filesystem = $subject->connect(__DIR__);
        $this->assertInstanceOf(LocalFileSystem::class, $filesystem);
        $subject->disconnect($filesystem);
        $this->expectException(FileNotFoundException::class);
        $subject->connect('Non-existing folder');
    }
}

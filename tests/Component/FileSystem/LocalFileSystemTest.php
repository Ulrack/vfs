<?php
/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace Ulrack\Vfs\Tests\Component\FileSystem;

use Ulrack\Vfs\Exception\FileNotFoundException;
use Ulrack\Vfs\Exception\InaccessibleFileException;
use Ulrack\Vfs\Common\FileInterface;
use Ulrack\Vfs\Component\FileSystem\LocalFileSystem;
use PHPUnit\Framework\TestCase;
use FilesystemIterator;

/**
 * @coversDefaultClass \Ulrack\Vfs\Component\FileSystem\LocalFileSystem
 * @covers \Ulrack\Vfs\Exception\InaccessibleFileException
 * @covers \Ulrack\Vfs\Exception\FileNotFoundException
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class LocalFileSystemTest extends TestCase
{
    /**
     * @return LocalFileSystem
     *
     * @covers ::__construct
     */
    public function testConstruct(): LocalFileSystem
    {
        $subject = new LocalFileSystem(__DIR__.'/../../test-filesystem');
        $this->assertInstanceOf(LocalFileSystem::class, $subject);

        return $subject;
    }

    /**
     * @depends testConstruct
     *
     * @param LocalFileSystem $subject
     *
     * @return void
     *
     * @covers ::touch
     * @covers ::unlink
     * @covers ::inBoundary
     * @covers ::toRealPath
     */
    public function testTouchAndUnlink(LocalFileSystem $subject): void
    {
        $subject->unlink('/foo.txt');
        $subject->touch('/foo.txt');
        $this->expectException(InaccessibleFileException::class);
        $subject->touch('../foo.txt');
    }

    /**
     * @depends testConstruct
     *
     * @param LocalFileSystem $subject
     *
     * @return void
     *
     * @covers ::makeDirectory
     * @covers ::removeDirectory
     * @covers ::isDirectory
     */
    public function testMakeAndRemoveDirectory(LocalFileSystem $subject): void
    {
        $subject->makeDirectory('foo');
        $this->assertEquals(true, $subject->isDirectory('foo'));
        $subject->removeDirectory('foo');
        $this->assertEquals(false, $subject->isDirectory('foo'));
    }

    /**
     * @depends testConstruct
     *
     * @param LocalFileSystem $subject
     *
     * @return void
     *
     * @covers ::list
     */
    public function testList(LocalFileSystem $subject): void
    {
        $this->assertEquals(['foo.txt'], $subject->list('/'));
        $this->assertEquals([], $subject->list('/bar'));
    }

    /**
     * @depends testConstruct
     *
     * @param LocalFileSystem $subject
     *
     * @return void
     *
     * @covers ::isReadable
     * @covers ::isWriteable
     * @covers ::isExecutable
     * @covers ::isFile
     */
    public function testFileChecks(LocalFileSystem $subject): void
    {
        $this->assertIsBool($subject->isReadable('foo.txt'));
        $this->assertIsBool($subject->isWriteable('foo.txt'));
        $this->assertIsBool($subject->isExecutable('foo.txt'));
        $this->assertIsBool($subject->isFile('foo.txt'));
    }

    /**
     * @depends testConstruct
     *
     * @param LocalFileSystem $subject
     *
     * @return void
     *
     * @covers ::move
     * @covers ::copy
     * @covers ::unlink
     * @covers ::isFile
     */
    public function testFileMove(LocalFileSystem $subject): void
    {
        $subject->move('foo.txt', 'bar.txt');
        $this->assertEquals(false, $subject->isFile('foo.txt'));
        $this->assertEquals(true, $subject->isFile('bar.txt'));
        $subject->copy('bar.txt', 'foo.txt');
        $this->assertEquals(true, $subject->isFile('foo.txt'));
        $this->assertEquals(true, $subject->isFile('bar.txt'));
        $subject->unlink('bar.txt');
    }

    /**
     * @depends testConstruct
     *
     * @param LocalFileSystem $subject
     *
     * @return void
     *
     * @covers ::put
     * @covers ::write
     * @covers ::truncate
     * @covers ::get
     * @covers ::size
     */
    public function testFileWriting(LocalFileSystem $subject): void
    {
        $subject->put('foo.txt', 'foo');
        $subject->write('foo.txt', 'bar');
        $this->assertEquals('foobar', $subject->get('foo.txt'));
        $this->assertEquals(6, $subject->size('foo.txt'));
        $subject->truncate('foo.txt');
        $this->assertEquals('', $subject->get('foo.txt'));
    }

    /**
     * @depends testConstruct
     *
     * @param LocalFileSystem $subject
     *
     * @return void
     *
     * @covers ::realpath
     */
    public function testRealPath(LocalFileSystem $subject): void
    {
        $this->assertRegexp(
            '/test-filesystem\/foo.txt/',
            $subject->realpath('foo.txt')
        );
    }

    /**
     * @depends testConstruct
     *
     * @param LocalFileSystem $subject
     *
     * @return void
     *
     * @covers ::setFileMode
     * @covers ::getFileMode
     */
    public function testFileMode(LocalFileSystem $subject): void
    {
        $subject->setFileMode('foo.txt', 0777);
        $this->assertEquals(777, $subject->getFileMode('foo.txt'));
    }

    /**
     * @depends testConstruct
     *
     * @param LocalFileSystem $subject
     *
     * @return void
     *
     * @covers ::getFileIterable
     * @covers ::getDirectoryIterable
     */
    public function testGetIterators(LocalFileSystem $subject): void
    {
        $this->assertInstanceOf(
            FileInterface::class,
            $subject->getFileIterable('foo.txt', FileInterface::MODE_CHUNK, 4)
        );

        $this->assertInstanceOf(
            FilesystemIterator::class,
            $subject->getDirectoryIterable('/')
        );

        $this->expectException(FileNotFoundException::class);
        $subject->getFileIterable('bar.txt');
    }

    /**
     * @depends testConstruct
     *
     * @param LocalFileSystem $subject
     *
     * @return void
     *
     * @covers ::getDirectoryIterable
     */
    public function testFailedDirectoryIterator(LocalFileSystem $subject): void
    {
        $this->expectException(FileNotFoundException::class);
        $subject->getDirectoryIterable('bar');
    }
}

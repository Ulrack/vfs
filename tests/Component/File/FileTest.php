<?php
/**
 * Copyright (C) Jyxon, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace Ulrack\Vfs\Tests\Component\File;

use Ulrack\Vfs\Common\FileInterface;
use Ulrack\Vfs\Component\File\File;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

/**
 * @coversDefaultClass \Ulrack\Vfs\Component\File\File
 */
class FileTest extends TestCase
{
    /**
     * @return File
     *
     * @covers ::__construct
     */
    public function testConstructChunkMode(): File
    {
        $resource = tmpfile();
        $subject = new File($resource, FileInterface::MODE_CHUNK, 4);
        $this->assertInstanceOf(File::class, $subject);

        return $subject;
    }

    /**
     * @return File
     *
     * @covers ::__construct
     */
    public function testConstructLineMode(): File
    {
        $resource = tmpfile();
        $subject = new File($resource, FileInterface::MODE_LINE);
        $this->assertInstanceOf(File::class, $subject);

        return $subject;
    }

    /**
     * @depends testConstructLineMode
     * @depends testConstructChunkMode
     *
     * @param File $lineSubject
     * @param File $chunkSubject
     *
     * @return File[]
     *
     * @covers ::offsetExists
     * @covers ::offsetSet
     * @covers ::offsetUnset
     * @covers ::offsetGet
     * @covers ::handleChunkWrite
     * @covers ::handleLineWrite
     * @covers ::mapToLine
     * @covers ::feof
     * @covers ::handleAppend
     */
    public function testArrayAccess(File $lineSubject, File $chunkSubject): array
    {
        $this->assertEquals(true, isset($lineSubject[0]));
        $this->assertEquals(false, isset($chunkSubject[0]));
        // Covers appending with fixed value
        $lineSubject[0] = 'foo';
        $chunkSubject[0] = 'foo';

        // Covers appending
        $lineSubject[] = 'baz';
        $chunkSubject[] = 'baz';

        // Covers overwriting
        $chunkSubject[0] = 'bar';
        $lineSubject[0] = 'bar';

        //Covers unsetting
        unset($lineSubject[1]);
        unset($chunkSubject[0]);

        $this->assertEquals('z', $chunkSubject[0]);
        $this->assertEquals('bar', $lineSubject[0]);

        // Covers setting with same chunk size
        $chunkSubject[0] = 'bara';
        $chunkSubject[1] = 'baza';

        // Covers overwriting the last chunk
        $chunkSubject[1] = 'barar';
        $lineSubject[1] = 'bar';
        $lineSubject[2] = 'baz';

        // Covers overwriting the a middle chunk
        $chunkSubject[1] = 'baz';
        $lineSubject[1] = 'ba';

        // Cover appending empty lines to offset
        $lineSubject[10] = 'qux';

        $this->assertEquals('qux', $lineSubject[10]);

        return [$lineSubject, $chunkSubject];
    }

    /**
     * @depends testArrayAccess
     *
     * @param File[] $subjects
     *
     * @return void
     *
     * @covers ::ensureIteratorPosition
     * @covers ::current
     * @covers ::key
     * @covers ::next
     * @covers ::rewind
     * @covers ::valid
     */
    public function testIterator(array $subjects): void
    {
        $lineMatchArray = [];
        // Line reader iteration
        foreach ($subjects[0] as $key => $line) {
            $lineMatchArray[$key] = $line;
            // This is to throw the internal pointer off balance.
            $this->assertEquals('ba', $subjects[0][1]);
        }

        $this->assertEquals(
            [
                'bar',
                'ba',
                'baz',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                'qux'
            ],
            $lineMatchArray
        );

        $chunkMatchArray = [];
        // Chunk reader iteration
        foreach ($subjects[1] as $key => $line) {
            $chunkMatchArray[$key] = $line;
            // This is to throw the internal pointer off balance.
            $this->assertEquals('bazr', $subjects[1][1]);
        }

        $this->assertEquals(
            [
                'bara',
                'bazr'
            ],
            $chunkMatchArray
        );
    }

    /**
     * @depends testConstructLineMode
     *
     * @param File $lineSubject
     *
     * @return void
     *
     * @covers ::offsetExists
     */
    public function testOffsetExistsFails(File $lineSubject): void
    {
        $this->expectException(InvalidArgumentException::class);
        $lineSubject->offsetExists('foo');
    }

    /**
     * @depends testConstructLineMode
     *
     * @param File $lineSubject
     *
     * @return void
     *
     * @covers ::offsetSet
     */
    public function testOffsetSetFails(File $lineSubject): void
    {
        $this->expectException(InvalidArgumentException::class);
        $lineSubject->offsetSet('foo', 'foo');
    }

    /**
     * @depends testConstructLineMode
     *
     * @param File $lineSubject
     *
     * @return void
     *
     * @covers ::offsetSet
     */
    public function testOffsetSetFailsValue(File $lineSubject): void
    {
        $this->expectException(InvalidArgumentException::class);
        $lineSubject->offsetSet(0, ['foo']);
    }

    /**
     * @depends testConstructLineMode
     *
     * @param File $lineSubject
     *
     * @return void
     *
     * @covers ::offsetGet
     */
    public function testOffsetGetFails(File $lineSubject): void
    {
        $this->expectException(InvalidArgumentException::class);
        $lineSubject->offsetGet('foo');
    }

    /**
     * @depends testConstructLineMode
     *
     * @param File $lineSubject
     *
     * @return void
     *
     * @covers ::offsetUnset
     */
    public function testOffsetUnsetFails(File $lineSubject): void
    {
        $this->expectException(InvalidArgumentException::class);
        $lineSubject->offsetUnset('foo');
    }

    /**
     * @return void
     *
     * @covers ::__construct
     * @covers ::close
     */
    public function testClose(): void
    {
        $subject = new File(tmpfile(), FileInterface::MODE_LINE);
        $this->assertInstanceOf(File::class, $subject);
        $subject->close();
    }
}

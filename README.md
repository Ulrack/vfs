# DEPRECATION NOTICE: this package has been moved and improved at [grizz-it/vfs](https://github.com/grizz-it/vfs)

[![Build Status](https://travis-ci.com/ulrack/vfs.svg?branch=master)](https://travis-ci.com/ulrack/vfs)

# Ulrack VFS

Ulrack VFS is a Virtual File System implementation in PHP.
It allows the creation of confined file and directory operation classes.

## Installation

To install the package run the following command:

```
composer require ulrack/vfs
```

## Usage

### Drivers

Drivers are build to connect to a file system.
This package only contains a driver for a local file system.
The drivers are build upon `Ulrack\Vfs\Common\FileSystemDriverInterface`.

#### Ulrack\Vfs\Component\Driver\LocalFileSystemDriver

This implementation serves as a factory for the `Ulrack\Vfs\Component\FileSystem\LocalFileSystem`.
An example implementation looks like the following:

```php
use Ulrack\Vfs\Driver\LocalFileSystemDriver;

$driver = new LocalFileSystemDriver();

$fileSystem = $driver->connect(__DIR__ . '/tests/test-filesystem');
```

This would yield a file system confined to the directory `ulrack/vfs/test/test-filesystem`.

### File Systems

File systems are based on `Ulrack\Vfs\Common\FileSystemInterface`.
File systems provide basic functionalities of PHP for file and directory manipulation,
but based on an interface. This simplifies changing the sources of your files and directories.

#### Ulrack\Vfs\Component\FileSystem\LocalFileSystem

This class implements the functionalities to manipulate a confined local file system.
The creation of this class is recommended to be done through the `LocalFileSystemDriver`,
but can also be performed without it:

```php
use Ulrack\Vfs\FileSystem\LocalFileSystem;

$filesystem = new LocalFileSystem(__DIR__ . '/tests/test-filesystem');
```

### Files

This package also provides an iterator class for files.
This iterator is build on the `Ulrack\Vfs\Common\FileInterface`.

#### Ulrack\Vfs\Component\File\File

This class is the implementation of the `FileInterface`.
It exposes iterator and `ArrayAccess` functionalities on a file.

Files can be opened in 2 different modes.

**MODE_CHUNK**

This mode iterates over the file in predetermined sized chunks.
The write operations completely override a chunk (even if the size is different).
The chunk size in this mode determines the amount of bytes to read.

**MODE_LINE**

This mode iterates over the file per line (using `PHP_EOL`).
The write operations completely override the line at the defined position.
The chunk size in this mode determines the maximum amount of bytes a line can be.

The `File` class can be created from a `LocalFileSystem` by calling `getFileIterable`.
To create one, open a file in `r+` mode and pass the resource together with the desired mode and a chunk size to the constructor:

```php
use Ulrack\Vfs\Common\FileInterface;
use Ulrack\Vfs\File\File;

$fileResource = fopen(__DIR__ . '/tests/test-filesystem/foo.txt');
$fileIterable = new File($fileResource, FileInterface::MODE_LINE);
```

The iterable can then be used, just like an array:

```php
// Write foo to the second line.
$fileIterable[1] = 'foo';

// Outputs foo
echo $fileIterable[1];

// Removes foo from the file.
unset($fileIterable[1]);

// Appends foo as a line to the end of the file.
$fileIterable[] = 'foo';

// Will output the line and line number for every line in the file.
foreach ($fileIterable as $key => $line) {
    echo sprintf('Line %d says: %s', $key + 1, $line);
}
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## MIT License

Copyright (c) 2019 GrizzIT

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.

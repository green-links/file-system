# File System

Common file system component used by a number of Green Links packages.

## Usage

There are 2 file managers in the file system:

- File manager (creates, reads, writes, deletes, and moves files)
- Source manager (Creates, runs, writes, deletes, and moves php source code files)

They can be created like this:

    $fileManager = \GreenLinks\FileSystem\FileManager('path/to/root/dir');
    $srcManager  = \GreenLinks\FileSystem\SrcManager('path/to/root/dir');

Each kind of manager has the following methods in common:

    write('path', 'contents): self
    move('old/path', 'new/path'): self
    delete('path'): self
    exists('path'): bool
    getRootDir(): string

The `write` method for the source manager will flush the opcache for the written
file if the opcache extension is installed.

Additionally, the file manager has a read method:

    read('path'): string

and the source manager has a run method:

    run('path'): mixed

The run method returns whatever is `return`ed by the source file that was run.

If any of the methods in either manager fail, then a
`GreenLinks\FileSystem\Exception` is thrown.

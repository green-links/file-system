<?php
declare(strict_types=1);

namespace GreenLinks\FileSystem;

use RuntimeException;

class Exception extends RuntimeException
{
    public static function parentAboveRoot(string $path): self
    {
        $message = sprintf('Path contains a reference '
            . 'to a directory above root: "%s".', $path);

        return new self($message);
    }

    public static function illegalParent(string $path): self
    {
        $message = sprintf('Path cannot contain '
            . 'references to a parent directory: "%s".', $path);

        return new self($message);
    }

    public static function tooLong(string $path): self
    {
        $message = sprintf('Path is longer than max '
            . 'permitted path length (%d): "%s".', $path);

        return new self($message);
    }

    public static function notFound(string $path): self
    {
        $message = sprintf('Path could not be found, please ensure that it '
            . 'exists and has appropriate permissions: "%s".', $path);

        return new self($message);
    }

    public static function notDirectory(string $path): self
    {
        $message = sprintf('Path is not a directory: "%s".', $path);

        return new self($message);
    }

    public static function notFile(string $path): self
    {
        $message = sprintf('Path is not a file: "%s".', $path);

        return new self($message);
    }

    public static function unknownPathType(string $path): self
    {
        $message = sprintf('Could not determine path type '
            . '(file or directory): "%s".', $path);

        return new self($message);
    }

    public static function alreadyExists(string $oldPath, string $newPath): self
    {
        $message = sprintf('Could not move path, destination already exists'
            . ': "%s" => "%s".', $oldPath, $newPath);

        return new self($message);
    }

    public static function move(string $oldPath, string $newPath): self
    {
        $message = sprintf(
            'File could not be moved: "%s" => "%s".',
            $oldPath,
            $newPath
        );

        return new self($message);
    }

    public static function create(string $path): self
    {
        $message = sprintf('Path could not be created: "%s".', $path);

        return new self($message);
    }

    public static function read(string $path): self
    {
        $message = sprintf('Path could not be read from: "%s".', $path);

        return new self($message);
    }

    public static function write(string $path): self
    {
        $message = sprintf('Path could not be written to: "%s".', $path);

        return new self($message);
    }

    public static function delete(string $path): self
    {
        $message = sprintf('Path could not be deleted: "%s".', $path);

        return new self($message);
    }
}

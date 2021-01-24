<?php
declare(strict_types=1);

namespace GreenLinks\FileSystem;

use function file_get_contents;
use function file_exists;

class FileManager extends Manager
{
    public function read(string $path): string
    {
        $fullPath = $this->checkPath($path);

        if (!file_exists($fullPath)) {
            throw Exception::notFound($fullPath);
        }

        $contents = @file_get_contents($fullPath);

        if (false === $contents) {
            throw Exception::read($fullPath);
        }

        return $contents;
    }
}

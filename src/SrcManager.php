<?php
declare(strict_types=1);

namespace GreenLinks\FileSystem;

use function opcache_invalidate;
use function function_exists;
use function file_exists;
use function is_readable;
use function is_file;

class SrcManager extends Manager
{
    public function write(string $path, string $contents): self
    {
        parent::write($path, $contents);

        if (function_exists('opcache_invalidate')) {
            $fullPath = $this->getRootDir() . $path;

            opcache_invalidate($fullPath);
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function run(string $path)
    {
        $fullPath = $this->checkPath($path);

        if (file_exists($fullPath) && is_readable($fullPath) && is_file($fullPath)) {
            return require $fullPath;
        }

        throw Exception::notFound($fullPath);
    }
}

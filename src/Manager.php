<?php
declare(strict_types=1);

namespace GreenLinks\FileSystem;

use function file_put_contents;
use function array_filter;
use function array_reduce;
use function array_merge;
use function file_exists;
use function array_walk;
use function preg_split;
use function array_pop;
use function in_array;
use function pathinfo;
use function implode;
use function is_file;
use function scandir;
use function is_dir;
use function rename;
use function strlen;
use function strpos;
use function unlink;
use function mkdir;
use function rmdir;

use const DIRECTORY_SEPARATOR;
use const PATHINFO_DIRNAME;
use const PHP_MAXPATHLEN;

abstract class Manager
{
    private string $rootDir;

    public function __construct(string $rootDir)
    {
        $parts = $this->formatPathParts($rootDir);

        $parts = array_reduce($parts, function (array $parts, string $part) use ($rootDir): array {
            if ('..' === $part) {
                if (empty($parts)) {
                    throw Exception::parentAboveRoot($rootDir);
                }

                array_pop($parts);
            } else {
                $parts = array_merge($parts, [$part]);
            }

            return $parts;
        }, []);

        $this->rootDir = implode(DIRECTORY_SEPARATOR, $parts) . DIRECTORY_SEPARATOR;
    }

    public function write(string $path, string $contents): self
    {
        $fullPath = $this->checkPath($path);
        $dirPath  = pathinfo($fullPath, PATHINFO_DIRNAME);

        if (file_exists($dirPath)) {
            if (!is_dir($dirPath)) {
                throw Exception::notDirectory($dirPath);
            }
        } elseif (!@mkdir($dirPath)) {
            throw Exception::create($dirPath);
        }

        if (file_exists($fullPath) && !is_file($fullPath)) {
            throw Exception::notFile($fullPath);
        }

        if (false === @file_put_contents($fullPath, $contents)) {
            throw Exception::write($fullPath);
        }

        return $this;
    }

    public function move(string $oldPath, string $newPath): self
    {
        $fullOldPath = $this->checkPath($oldPath);
        $fullNewPath = $this->checkPath($newPath);

        if (!file_exists($fullOldPath)) {
            throw Exception::notFound($fullOldPath);
        }

        if (file_exists($fullNewPath)) {
            throw Exception::alreadyExists($fullOldPath, $fullNewPath);
        }

        if (!@rename($fullOldPath, $fullNewPath)) {
            throw Exception::move($fullOldPath, $fullNewPath);
        }

        return $this;
    }

    public function delete(string $path): self
    {
        $fullPath = $this->checkPath($path);

        if (is_file($fullPath)) {
            if (!@unlink($fullPath)) {
                throw Exception::delete($fullPath);
            }

            return $this;
        }

        if (is_dir($fullPath)) {
            $children = @scandir($fullPath);

            if (false === $children) {
                throw Exception::read($fullPath);
            }

            array_walk($children, function (string $child) use ($path, $fullPath): void {
                if (in_array($child, ['.', '..'])) {
                    return;
                }

                $this->delete($path . '/' . $child);
            });

            if (!@rmdir($fullPath)) {
                throw Exception::delete($fullPath);
            }

            return $this;
        }

        throw Exception::unknownPathType($fullPath);
    }

    public function exists(string $path): bool
    {
        return file_exists(
            $this->checkPath($path)
        );
    }

    public function getRootDir(): string
    {
        return $this->rootDir;
    }

    protected function checkPath(string $path): string
    {
        $path = implode(DIRECTORY_SEPARATOR, $this->formatPathParts($path));

        if (false === strpos($path, DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR)) {
            $fullPath = $this->rootDir . $path;

            if (strlen($fullPath) > PHP_MAXPATHLEN) {
                throw Exception::tooLong($fullPath);
            }

            return $fullPath;
        }

        throw Exception::illegalParent($path);
    }

    private function formatPathParts($path): array
    {
        return array_filter(preg_split('~[/\\\\]~', $path), function (string $part): bool {
            return !in_array($part, ['', '.']);
        });
    }
}

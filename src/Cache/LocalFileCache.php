<?php
declare(strict_types=1);

/*
 * Licensed under MIT. See file /LICENSE.
 */

namespace App\Cache;

use Symfony\Component\Filesystem\Exception\FileNotFoundException;

class LocalFileCache implements CacheInterface
{
    /** @var string */
    private $basePath;

    public function __construct(
        string $basePath
    ) {
        $this->basePath = $basePath;
    }

    private function hasByFullPath(string $fullPath): bool
    {
        return \file_exists($fullPath);
    }

    public function has(string $key, bool $hashedKey = false): bool
    {
        if ($hashedKey) {
            $fileName = $key;
        } else {
            $fileName = \sha1($key);
        }

        $fullPath = $this->basePath . DIRECTORY_SEPARATOR . $fileName;
        return $this->hasByFullPath($fullPath);
    }

    /**
     * @inheritDoc
     */
    public function get(string $key, bool $hashedKey = false)
    {
        if ($hashedKey) {
            $fileName = $key;
        } else {
            $fileName = \sha1($key);
        }

        $fullPath = $this->basePath . DIRECTORY_SEPARATOR . $fileName;
        if ($this->hasByFullPath($fullPath)) {
            return \file_get_contents($fullPath);
        }

        throw new FileNotFoundException('Not found: ' . $fullPath);
    }

    /**
     * @inheritDoc
     */
    public function getUnserialized(string $key, bool $hashedKey = false)
    {
        if ($hashedKey) {
            $fileName = $key;
        } else {
            $fileName = \sha1($key);
        }

        $fullPath = $this->basePath . DIRECTORY_SEPARATOR . $fileName;
        if ($this->hasByFullPath($fullPath)) {
            return \unserialize(\file_get_contents($fullPath));
        }

        throw new FileNotFoundException('Not found: ' . $fullPath);
    }

    public function getStreamed(string $key, bool $hashedKey = false): void
    {
        if ($hashedKey) {
            $fileName = $key;
        } else {
            $fileName = \sha1($key);
        }

        $fullPath = $this->basePath . DIRECTORY_SEPARATOR . $fileName;
        if ($this->hasByFullPath($fullPath)) {
            \readfile($fullPath);
        } else {
            throw new FileNotFoundException('Not found: ' . $fullPath);
        }
    }

    /**
     * @inheritDoc
     */
    public function set(string $key, $value): void
    {
        $this->assureBasePathExists();

        $fileName = \sha1($key);
        \file_put_contents(
            $this->basePath . DIRECTORY_SEPARATOR . $fileName,
            $value
        );
    }

    /**
     * @inheritDoc
     */
    public function setSerialized(string $key, $value): void
    {
        $this->assureBasePathExists();

        $fileName = \sha1($key);
        \file_put_contents(
            $this->basePath . DIRECTORY_SEPARATOR . $fileName,
            \serialize($value)
        );
    }

    private function assureBasePathExists(): void
    {
        if (true === @is_dir($this->basePath)) {
            return;
        }

        $r = \mkdir($this->basePath, 0777, true);
        if (false === $r) {
            throw new \RuntimeException('Could not create cache directory at ' . $this->basePath);
        }
    }

    public function clear(): void
    {
        $this->assureBasePathExists();
        $this->deleteDirectoryContentsRecursively($this->basePath, true);
    }

    private function deleteDirectoryContentsRecursively(string $path, bool $keep = true): void
    {
        if (\is_dir($path)) {
            $objects = \scandir($path);
            foreach ($objects as $object) {
                if ($object !== '.' && $object != '..') {
                    if (\is_dir($path . DIRECTORY_SEPARATOR . $object) && !\is_link($path . DIRECTORY_SEPARATOR . $object)) {
                        $this->deleteDirectoryRecursively($path . DIRECTORY_SEPARATOR . $object, false);
                    } else {
                        \unlink($path . DIRECTORY_SEPARATOR . $object);
                    }
                }
            }

            if (false === $keep) {
                \rmdir($path);
            }
        }
    }

    public function getHashedKey(string $key): string
    {
        return \sha1($key);
    }

    public function getFullFilePath(string $key): string
    {
        $fileName = \sha1($key);
        return $this->basePath . DIRECTORY_SEPARATOR . $fileName;
    }
}

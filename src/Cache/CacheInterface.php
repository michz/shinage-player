<?php
declare(strict_types=1);

/*
 * Licensed under MIT. See file /LICENSE.
 */

namespace App\Cache;

interface CacheInterface
{
    public function has(string $key, bool $hashedKey = false): bool;

    /**
     * @return mixed The cached file (raw)
     */
    public function get(string $key, bool $hashedKey = false);

    /**
     * @return mixed The cached file (unserialized php object)
     */
    public function getUnserialized(string $key, bool $hashedKey = false);

    public function getStreamed(string $key, bool $hashedKey = false): void;

    /**
     * @param mixed $value The file contents to cache (raw)
     */
    public function set(string $key, $value): void;

    /**
     * @param mixed $value The file contents to cache (and serialize them)
     */
    public function setSerialized(string $key, $value): void;

    public function clear(): void;

    public function getHashedKey(string $key): string;

    public function getFullFilePath(string $key): string;
}

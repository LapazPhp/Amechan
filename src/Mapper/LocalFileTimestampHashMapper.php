<?php
namespace Lapaz\Amechan\Mapper;

/**
 * Local filesystem timestamp hash mapper.
 *
 * This class is useful when file content scanning become too slow.
 * Default hash function is `md5()`. This function can be replaced to any callable.
 */
class LocalFileTimestampHashMapper extends LocalFileHashMapper
{
    /**
     * LocalFileTimestampHashMapper constructor.
     *
     * @param string $baseUrl Base URL.
     * @param string $baseDir Local filesystem directory corresponding base URL,
     * @param callable|string|null $matcher Name based filter callback or regexp.
     * @param string|null $parameter Query parameter name revision hashed value.
     * @param callable|string $hashFunction Hash generator function which would be passed a timestamp value.
     */
    public function __construct($baseUrl, $baseDir, $matcher = null, $parameter = null, $hashFunction = 'md5')
    {
        parent::__construct($baseUrl, $baseDir, $matcher, $parameter, $hashFunction);
    }

    /**
     * @inheritDoc
     */
    protected function generateHash($file)
    {
        $timestamp = filemtime($file);

        if ($timestamp == false) {
            throw new \RuntimeException('Failed to retrieve file timestamp: ' . $file);
        }

        return call_user_func($this->hashFunction, $timestamp);
    }
}

<?php
namespace Lapaz\Amechan\Mapper;

/**
 * Local filesystem based mapper implementation.
 *
 * Default behavior is to append hash value based on file content by `md5_hash()`.
 * Users can customize hash function and query parameter name.
 */
class LocalFileHashMapper extends AbstractLocalFileMapper
{
    /**
     * Query parameter name revision hashed value.
     * If null, it modifies as `.../style.css?<hash value>` style, otherwise `.../style.css?<parameter>=<hash value>`.
     *
     * @var string|null
     */
    protected $parameter;

    /**
     * Hash generator function which retrieves single value of hash source.
     *
     * @var callable|string
     */
    protected $hashFunction;

    /**
     * LocalFileHashMapper constructor.
     *
     * @param string $baseUrl Base URL.
     * @param string $baseDir Local filesystem directory corresponding base URL,
     * @param callable|string|null $matcher Name based filter callback or regexp.
     * @param string|null $parameter
     * @param callable|string $hashFunction
     */
    public function __construct($baseUrl, $baseDir, $matcher = null, $parameter = null, $hashFunction = 'md5_file')
    {
        parent::__construct($baseUrl, $baseDir, $matcher);
        $this->parameter = $parameter;
        $this->hashFunction = $hashFunction;
    }

    /**
     * @inheritDoc
     */
    protected function modifyUrl($url, $file)
    {
        return $this->appendQueryParameter($url, $this->generateHash($file));
    }

    /**
     * Generates some hash value of given file.
     *
     * @param string $file File path corresponding the URL.
     * @return string Hash string to be added.
     */
    protected function generateHash($file)
    {
        return call_user_func($this->hashFunction, $file);
    }

    private function appendQueryParameter($url, $hash)
    {
        $glu = empty(parse_url($url, PHP_URL_QUERY)) ? '?' : '&';

        if ($this->parameter) {
            return $url . $glu . $this->parameter . '=' . urlencode($hash);
        } else {
            return $url . $glu . urlencode($hash);
        }
    }
}

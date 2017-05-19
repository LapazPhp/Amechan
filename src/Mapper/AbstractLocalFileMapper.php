<?php
namespace Lapaz\Amechan\Mapper;

use Lapaz\Amechan\UrlMapperInterface;
use Webmozart\PathUtil\Path;

/**
 * Local filesystem based mapper abstraction.
 */
abstract class AbstractLocalFileMapper implements UrlMapperInterface
{
    protected $baseUrl;

    protected $baseDir;

    protected $matcher;

    /**
     * AbstractLocalFileMapper constructor.
     *
     * @param string $baseUrl Base URL.
     * @param string $baseDir Local filesystem directory corresponding base URL,
     * @param callable|string|null $matcher Name based filter callback or regexp.
     */
    public function __construct($baseUrl, $baseDir, $matcher = null)
    {
        $this->baseUrl = $baseUrl;
        $this->baseDir = $baseDir;
        $this->matcher = $matcher;
    }

    /**
     * Modifies URL based on corresponding local file.
     *
     * @param string $url Source URL.
     * @param string $file File path corresponding to URL.
     * @return string Modified URL.
     */
    abstract protected function modifyUrl($url, $file);

    /**
     * @inheritDoc
     */
    public function apply($url)
    {
        if (strpos($url, $this->baseUrl) !== 0) {
            return $url;
        }

        if (!$this->applyMatcher($url)) {
            return $url;
        }

        $path = substr($url, strlen($this->baseUrl));
        $file = Path::join([$this->baseDir, $path]);
        if (!is_file($file)) {
            throw new \UnexpectedValueException('Target resource is not a file: ' . $file);
        }

        return $this->modifyUrl($url, $file);
    }

    private function applyMatcher($url)
    {
        if ($this->matcher === null) {
            return true;
        }
        if (is_callable($this->matcher)) {
            return (bool)call_user_func($this->matcher, $url);
        } else {
            return preg_match($this->matcher, $url) === 1;
        }
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: MrSir
 * Date: 3/10/2018
 * Time: 1:24 PM
 */

namespace emPHPasis\Pipelines\Passables;

/**
 * Class Initialize
 * @package emPHPasis\Pipelines\Passables
 */
class Initialize extends Passable
{
    /** @var string DEFAULT_CONFIG_PATH */
    const DEFAULT_CONFIG_PATH = "./";

    /** @var string DEFAULT_CONFIG_FILE */
    const DEFAULT_CONFIG_FILE = "emPHPasis.json";

    /** @var array $config */
    protected $config = [];

    /** @var string $path */
    protected $path = '';

    /** @var string $filePath */
    protected $filePath = '';

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * @param array $config
     */
    public function setConfig(array $config): void
    {
        $this->config = $config;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getFilePath(): string
    {
        return $this->filePath;
    }

    /**
     * @param string $filePath
     */
    public function setFilePath(string $filePath): void
    {
        $this->filePath = $filePath;
    }
}

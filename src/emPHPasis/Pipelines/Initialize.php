<?php
/**
 * Created by PhpStorm.
 * User: MrSir
 * Date: 3/10/2018
 * Time: 12:45 PM
 */

namespace emPHPasis\Pipelines;

use emPHPasis\Pipelines\Passables;
use emPHPasis\Pipelines\Pipes\Initialize\CreateConfig;
use emPHPasis\Pipelines\Pipes\Initialize\GenerateBaseConfig;
use emPHPasis\Pipelines\Pipes\Initialize\InsertConfigurations;
use emPHPasis\Pipelines\Pipes\Initialize\InsertReportsPaths;

/**
 * Class Initialize
 * @package emPHPasis\Pipelines
 */
class Initialize extends Pipeline
{
    /**
     * This is the fill function, it initializes the pipeline
     *
     * @param string $path
     *
     * @return $this
     */
    public function fill(string $path = null)
    {
        $passable = new Passables\Initialize();
        $passable->setPath(Passables\Initialize::DEFAULT_CONFIG_PATH);

        if ($path !== null && $path != '') {
            $passable->setPath($path);
        }

        $this->setPassable($passable);

        return $this;
    }

    /**
     * This is the flush function, it executes the entire pipe
     * @return Passables\Initialize
     */
    public function flush()
    {
        return $this->send($this->getPassable())
            ->through(
                [
                    GenerateBaseConfig::class,
                    InsertReportsPaths::class,
                    InsertConfigurations::class,
                    CreateConfig::class,
                ]
            )
            ->then(
                function (Passables\Initialize $passable) {
                    //TODO resolve what the pipe returns
                    return $passable->getConfig();
                }
            );
    }
}

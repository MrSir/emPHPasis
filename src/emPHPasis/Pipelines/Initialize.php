<?php
/**
 * Created by PhpStorm.
 * User: MrSir
 * Date: 3/10/2018
 * Time: 12:45 PM
 */

namespace emPHPasis\Pipelines;

use emPHPasis\Pipelines\Passables;
use emPHPasis\Pipelines\Pipes\Initialize\GenerateBaseConfig;

class Initialize extends Pipeline
{
    /**
     * This is the fill function, it initializes the pipeline
     *
     * @param string $path
     *
     * @return $this
     */
    public function fill(string $path = Passables\Initialize::DEFAULT_CONFIG_PATH)
    {
        $passable = new Passables\Initialize();
        $passable->setPath($path);

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
                    //TODO insert report directories
                    //TODO template configuration settings
                    //TODO format config and write file
                ]
            )
            ->then(
                function (Passables\Initialize $passable) {
                    return $passable->getResponse();
                }
            );
    }
}

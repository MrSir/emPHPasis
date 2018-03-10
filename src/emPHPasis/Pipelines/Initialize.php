<?php
/**
 * Created by PhpStorm.
 * User: MrSir
 * Date: 3/10/2018
 * Time: 12:45 PM
 */

namespace emPHPasis\Pipelines;

use emPHPasis\Passables\Passable;

class Initialize extends Pipeline
{
    /**
     * This is the fill function, it initializes the pipeline
     *
     * @return $this
     */
    public function fill()
    {
        $passable = new Passable();

        $this->setPassable($passable);

        return $this;
    }

    /**
     * This is the flush function, it executes the entire pipe
     * @return Passable
     */
    public function flush()
    {
        return $this->send($this->getPassable())
            ->through(
                [
                    //TODO generate base json object
                    //TODO insert report directories
                    //TODO template configuration settings
                ]
            )
            ->then(
                function (Passable $passable) {
                    return $passable->getResponse();
                }
            );
    }
}

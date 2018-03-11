<?php
/**
 * Created by PhpStorm.
 * User: MrSir
 * Date: 3/10/2018
 * Time: 12:45 PM
 */

namespace emPHPasis\Pipelines;

use emPHPasis\Pipelines\Passables;
use emPHPasis\Pipelines\Pipes\Generate\CreateConfig;
use emPHPasis\Pipelines\Pipes\Generate\FindConfig;
use emPHPasis\Pipelines\Pipes\Generate\GatherReports;
use emPHPasis\Pipelines\Pipes\Generate\GenerateBaseConfig;
use emPHPasis\Pipelines\Pipes\Generate\InsertConfigurations;
use emPHPasis\Pipelines\Pipes\Generate\InsertReportsPaths;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Generate
 * @package emPHPasis\Pipelines
 */
class Generate extends Pipeline
{
    /**
     * This is the fill function, it initializes the pipeline
     *
     * @param OutputInterface $outputInterface
     * @param string $path
     *
     * @return $this
     */
    public function fill(OutputInterface $outputInterface, string $config = null, string $path = null)
    {
        $passable = new Passables\Generate();
        $passable->setOutputInterface($outputInterface);
        $passable->setPath(Passables\Generate::DEFAULT_PATH);
        $passable->setConfig(Passables\Generate::DEFAULT_CONFIG_PATH);

        // reset the path if provided
        if ($path !== null && $path != '') {
            $passable->setPath($path);
        }

        // reset the config if provided
        if ($config !== null && $config != '') {
            $passable->setConfig($config);
        }

        $this->setPassable($passable);

        return $this;
    }

    /**
     * This is the flush function, it executes the entire pipe
     * @return Passables\Generate
     */
    public function flush()
    {
        return $this->send($this->getPassable())
            ->through(
                [
                    FindConfig::class,
                    GatherReports::class,

                    // PHPUnit Analysis
                    //TODO generate Tests analysis
                    //TODO generate Code Coverage Analysis
                    //TODO generate Crap analysis

                    // PHPCS Analysis
                    //TODO generate Check Style Analysis

                    // PHPMD Analysis
                    //TODO generate Mess Detection Analysis

                    // PHPCPD Analysis
                    //TODO generate Copy/Paste Duplication Analysis

                    // PDEPEND Analysis
                    //TODO generate Code Dependency Analysis

                    // Combined Indexes Analysis
                    //TODO
                ]
            )
            ->then(
                function (Passables\Generate $passable) {
                    return $passable;
                }
            );
    }
}

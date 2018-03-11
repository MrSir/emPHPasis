<?php
/**
 * Created by PhpStorm.
 * User: MrSir
 * Date: 3/10/2018
 * Time: 1:40 PM
 */

namespace emPHPasis\Pipelines\Pipes\Generate;

use Carbon\Carbon;
use Closure;
use emPHPasis\Exceptions\Pipelines\Pipes\Generate\GatherReportsException;
use emPHPasis\Pipelines\Passables\Generate;
use emPHPasis\Pipelines\Pipes\Pipe;
use Exception;
use Throwable;

/**
 * Class GatherReports
 * @package emPHPasis\Pipelines\Pipes\Generate
 */
class GatherReports extends Pipe
{
    /**
     * GatherReports constructor.
     */
    public function __construct()
    {
        parent::__construct(GatherReportsException::class);
    }

    /**
     * @param Generate $passable
     * @param Closure  $next
     *
     * @return mixed
     * @throws Exception
     */
    public function handle(Generate &$passable, Closure $next)
    {
        try {
            // grab the parameters from the passable
            $code = $passable->getCode();
            $result = $passable->getResult();

            // skip the pipe if the previous has failed
            if ($code == $passable::SUCCESS_CODE) {
                $decodedConfig = $passable->getDecodedConfig();

                // set the successful code and result
                $code = $passable::EXCEPTION_CODE;
                $result = ['errors' => ['No reports present in config.']];

                // set flags
                if (property_exists($decodedConfig, 'reports')) {
                    // set PHPUnit flags
                    if (property_exists($decodedConfig->reports, 'phpunit')) {
                        // set clover flag
                        if (property_exists($decodedConfig->reports->phpunit, 'clover')) {
                            $passable->setCloverReport(true);
                        }

                        // set coverage xml flag
                        if (property_exists($decodedConfig->reports->phpunit, 'coverage-xml')) {
                            $passable->setCoverageXMLReport(true);
                        }

                        // set coverage crap4j flag
                        if (property_exists($decodedConfig->reports->phpunit, 'coverage-crap4j')) {
                            $passable->setCoverageCRAP4JReport(true);
                        }

                        // set junit flag
                        if (property_exists($decodedConfig->reports->phpunit, 'coverage-crap4j')) {
                            $passable->setCoverageCRAP4JReport(true);
                        }
                    }

                    // set PHPMD flags
                    if (property_exists($decodedConfig->reports, 'phpmd')) {
                        $passable->setPmdReport(true);
                    }

                    // set PHPCS flags
                    if (property_exists($decodedConfig->reports, 'phpcs')) {
                        $passable->setCheckstyleReport(true);
                    }

                    // set PHPCPD flags
                    if (property_exists($decodedConfig->reports, 'phpcpd')) {
                        $passable->setPmdCPDReport(true);
                    }

                    // set PDEPEND flags
                    if (property_exists($decodedConfig->reports, 'pdepend')) {
                        // set jdepend xml flag
                        if (property_exists($decodedConfig->reports->pdepend, 'jdepend-xml')) {
                            $passable->setJdependXMLReport(true);
                        }

                        // set jdepend chart flag
                        if (property_exists($decodedConfig->reports->pdepend, 'jdepend-chart')) {
                            $passable->setJdependChartSVG(true);
                        }

                        // set overview pyramid flag
                        if (property_exists($decodedConfig->reports->pdepend, 'overview-pyramid')) {
                            $passable->setOverviewPyramidSVG(true);
                        }
                    }

                    // set PHPLOC flags
                    if (property_exists($decodedConfig->reports, 'phploc')) {
                        $passable->setPhplocReport(true);
                    }

                    $passable->getOutputInterface()
                        ->writeln(Carbon::now() . ' Gathered reports.');

                    // set the successful code and result
                    $code = $passable::SUCCESS_CODE;
                    $result = ['message' => 'Success'];
                }
            }

            // set the code and result
            $passable->setCode($code);
            $passable->setResult($result);
        } catch (Throwable $e) {
            $exceptionType = $this->getExceptionType();

            throw new $exceptionType($e);
        }

        return $next($passable);
    }

    /**
     * This is a wrapper function to aid in testing the pipe.
     * It writes the config contents to a file
     *
     * @param string $file
     * @param string $data
     *
     * @return bool|int
     */
    public function writeFile(string $file, string $data)
    {
        return file_put_contents(
            $file,
            $data
        );
    }
}

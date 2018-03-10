<?php
/**
 * Created by PhpStorm.
 * User: MrSir
 * Date: 3/10/2018
 * Time: 1:40 PM
 */

namespace emPHPasis\Pipelines\Pipes\Initialize;

use Closure;
use emPHPasis\Exceptions\Pipelines\Pipes\Initialize\InsertReportsPathsException;
use emPHPasis\Pipelines\Passables\Initialize;
use emPHPasis\Pipelines\Pipes\Pipe;
use Exception;
use Throwable;

/**
 * Class InsertReportsPaths
 * @package emPHPasis\Pipelines\Pipes\Initialize
 */
class InsertReportsPaths extends Pipe
{
    /**
     * InsertReportsPaths constructor.
     */
    public function __construct()
    {
        parent::__construct(InsertReportsPathsException::class);
    }

    /**
     * @param Initialize $passable
     * @param Closure    $next
     *
     * @return mixed
     * @throws Exception
     */
    public function handle(Initialize &$passable, Closure $next)
    {
        try {
            // grab the parameters from the passable
            $code = $passable->getCode();
            $result = $passable->getResult();
            $config = $passable->getConfig();

            // skip the pipe if the previous has failed
            if ($code == 200) {
                // set the reports section
                $config['reports'] = [
                    'phpunit' => [
                        'clover' => 'build/logs/clover.xml',
                        'coverage-xml' => 'build/logs/coverage/index.xml',
                        'coverage-crap4j' => 'build/logs/crap4j.xml',
                        'junit' => 'build/logs/junit.xml',
                    ],
                    'phpmd' => 'build/logs/phpmd.xml',
                    'phpcs' => 'build/logs/checkstyle.xml',
                    'phpcpd' => 'build/logs/pmd-cpd.xml',
                    'pdepend' => [
                        'jdepend-xml' => 'build/logs/jdepend.xml',
                        'jdepend-chart' => 'build/pdepend/dependencies.svg',
                        'overview-pyramid' => 'build/pdepend/overview-pyramid.svg',
                    ],
                    'phploc' => 'build/logs/phploc.xml',
                ];

                // reset the config
                $passable->setConfig($config);

                $code = 200;
                $result = ['message' => 'Success'];
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
}

<?php
/**
 * Created by PhpStorm.
 * User: MrSir
 * Date: 3/10/2018
 * Time: 1:40 PM
 */

namespace emPHPasis\Pipelines\Pipes\Initialize;

use Closure;
use emPHPasis\Exceptions\Pipelines\Pipes\Initialize\InsertConfigurationsException;
use emPHPasis\Pipelines\Passables\Initialize;
use emPHPasis\Pipelines\Pipes\Pipe;
use Exception;
use Throwable;

/**
 * Class InsertConfigurations
 * @package emPHPasis\Pipelines\Pipes\Initialize
 */
class InsertConfigurations extends Pipe
{
    /**
     * InsertConfigurations constructor.
     */
    public function __construct()
    {
        parent::__construct(InsertConfigurationsException::class);
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
            if ($code == $passable::SUCCESS_CODE) {
                // set the reports section
                $config['configurations'] = [
                    'template_path' => 'vendor/MrSir/emPHPasis/templates/basic2018/',
                    'report_directory' => 'build/emPHPasis/',
                    'paths' => [
                        "./src/",
                        "./tests/",
                    ],
                    "thresholds" => [
                        "classes" => [
                            "high" => [
                                "background_class" => "bg-green",
                                "icon_class" => "fa-check-circle",
                            ],
                            "medium" => [
                                "background_class" => "bg-yellow",
                                "icon_class" => "fa-exclamation-circle",
                            ],
                            "low" => [
                                "background_class" => "bg-red",
                                "icon_class" => "fa-times-circle",
                            ],
                        ],
                        "testability" => [
                            "index" => [
                                "high" => 75,
                                "low" => 35,
                            ],
                            "assertionsTestRatio" => [
                                "high" => 5,
                                "low" => 2,
                            ],
                            "testsMethodRatio" => [
                                "high" => 3,
                                "low" => 1,
                            ],
                            "percentTestsPassing" => [
                                "high" => 90,
                                "low" => 50,
                            ],
                            "tests" => [
                                "high" => 1000,
                                "low" => 250,
                            ],
                            "assertions" => [
                                "high" => 5000,
                                "low" => 2000,
                            ],
                            "lines" => [
                                "high" => 75,
                                "low" => 35,
                            ],
                            "methods" => [
                                "high" => 75,
                                "low" => 35,
                            ],
                            "classes" => [
                                "high" => 75,
                                "low" => 35,
                            ],
                            "elements" => [
                                "high" => 75,
                                "low" => 35,
                            ],
                        ],
                    ],
                ];

                // reset the config
                $passable->setConfig($config);

                // set the successful code and result
                $code = $passable::SUCCESS_CODE;
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

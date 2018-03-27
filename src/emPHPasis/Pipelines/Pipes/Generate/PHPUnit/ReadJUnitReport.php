<?php
/**
 * Created by PhpStorm.
 * User: MrSir
 * Date: 3/10/2018
 * Time: 1:40 PM
 */

namespace emPHPasis\Pipelines\Pipes\Generate\PHPUnit;

use Carbon\Carbon;
use Closure;
use emPHPasis\Exceptions\Pipelines\Pipes\Generate\GatherReportsException;
use emPHPasis\Pipelines\Passables\Generate;
use emPHPasis\Pipelines\Pipes\Pipe;
use emPHPasis\Traits\NumberFormatters;
use Exception;
use SimpleXMLElement;
use Throwable;

/**
 * Class ReadJUnitReport
 * @package emPHPasis\Pipelines\Pipes\Generate\PHPUnit
 */
class ReadJUnitReport extends Pipe
{
    use NumberFormatters;

    /**
     * GatherReports constructor.
     */
    public function __construct()
    {
        parent::__construct(Exception::class);
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
                // set the successful code and result
                $code = $passable::EXCEPTION_CODE;
                $result = ['errors' => ['No source report found.']];

                // check if a source xml is present
                if ($passable->hasSourceReport()) {
                    $decodedConfig = $passable->getDecodedConfig();
                    $reportData = $passable->getReportData();

                    $xml = simplexml_load_file($decodedConfig->reports->phpunit->junit);

                    $testSuiteAttributes = $xml->testsuite->attributes();

                    $reportData['phpunit']['junit'] = [
                        'totals' => [
                            'tests' => [
                                'number' => (int)$testSuiteAttributes->tests,
                                'errors' => (int)$testSuiteAttributes->errors,
                                'failures' => (int)$testSuiteAttributes->failures,
                                'skipped' => (int)$testSuiteAttributes->skipped,
                            ],
                            'assertions' => (int)$testSuiteAttributes->assertions,
                            'time' => (string)$this->convertSecondsToHours($testSuiteAttributes->time),
                        ],
                        'tests' => [],
                    ];

                    $this->readChildren($xml->testsuite->children(), $reportData);

                    $passable->setReportData($reportData);

                    $passable->getOutputInterface()
                        ->writeln(Carbon::now() . ' Read junit report');

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

    private function readChildren($testSuites, array &$reportData)
    {
        foreach ($testSuites as $testSuite) {
            $attributes = $testSuite->attributes();

            $passedTests = (int)($attributes->tests - $attributes->errors - $attributes->failures - $attributes->skipped);

            $reportData['phpunit']['junit']['tests'][] = [
                'class' => (string)$attributes->name,
                'assertions' => (int)$attributes->assertions,
                'time' => number_format((float)$attributes->time, 3) . ' s',
                'tests' => [
                    'total' => (int)$attributes->tests,
                    'passed' => $passedTests,
                    'errors' => (int)$attributes->errors,
                    'failures' => (int)$attributes->failures,
                    'skipped' => (int)$attributes->skipped,
                ],
                'testsPercentage' => [
                    'passed' => $this->formatRatio($passedTests, $attributes->tests, 2, true),
                    'errors' => $this->formatRatio($attributes->errors, $attributes->tests, 2, true),
                    'failures' => $this->formatRatio($attributes->failures, $attributes->tests, 2, true),
                    'skipped' => $this->formatRatio($attributes->skipped, $attributes->tests, 2, true),
                ]
            ];
        }
    }
}

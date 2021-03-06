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
use emPHPasis\Exceptions\Pipelines\Pipes\Generate\CompileTemplateException;
use emPHPasis\Pipelines\Passables\Generate;
use emPHPasis\Pipelines\Pipes\Pipe;
use emPHPasis\Traits\NumberFormatters;
use Exception;
use Pug\Pug;
use stdClass;
use Throwable;

/**
 * Class CompileTestabilityTemplate
 * @package emPHPasis\Pipelines\Pipes\Generate
 */
class CompileTestabilityTemplate extends Pipe
{
    use NumberFormatters;

    /**
     * CompileTemplate constructor.
     */
    public function __construct()
    {
        parent::__construct(CompileTemplateException::class);
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
                $reportData = $passable->getReportData();

                // set the successful code and result
                $code = $passable::EXCEPTION_CODE;
                $result = ['errors' => ['No reports template found.']];

                $reportPath = $decodedConfig->configurations->report_path;
                $templatePath = $decodedConfig->configurations->template_path;

                // check if the template exists
                if (file_exists($templatePath)) {
                    // instantiate pug engine
                    $pugEngine = new Pug();

                    $coverageXML = $reportData['phpunit']['coverageXML'];
                    $cloverXML = $reportData['phpunit']['cloverXML'];
                    $junit = $reportData['phpunit']['junit'];

                    $thresholds = $decodedConfig->configurations->thresholds;

                    $tests = $junit['totals']['tests'];

                    // analysis
                    $assertionsTestRatio = $this->formatRatio(
                        $junit['totals']['assertions'],
                        $tests['number']
                    );
                    $testsMethodRatio = $this->formatRatio(
                        $tests['number'],
                        $coverageXML['methods']['executable']
                    );
                    $percentTestsPassing = $this->formatRatio(
                        $tests['number'] - $tests['errors'] - $tests['failures'] - $tests['skipped'],
                        $tests['number'],
                        2,
                        true
                    );

                    // indexGraph values
                    $assertionsTestRatioNormalized = $this->normalize(
                        $assertionsTestRatio,
                        $thresholds->testability->assertionsTestRatio->high
                    );
                    $testsMethodRatioNormalized = $this->normalize(
                        $testsMethodRatio,
                        $thresholds->testability->testsMethodRatio->high
                    );
                    $percentTestsPassingNormalized = $this->normalize(
                        $percentTestsPassing,
                        $thresholds->testability->percentTestsPassing->high
                    );

                    $data = [
                        'route' => 'testability',
                        'title' => 'Testability',
                        'subject' => 'Code coverage and test analysis.',
                        'indexGraph' => [
                            'assertionsTestRatio' => $assertionsTestRatioNormalized,
                            'testsMethodRatio' => $testsMethodRatioNormalized,
                            'percentTestsPassing' => $percentTestsPassingNormalized,
                        ],
                        'analysis' => [
                            'index' => $this->computeIndex(
                                [
                                    $assertionsTestRatioNormalized,
                                    $testsMethodRatioNormalized,
                                    $percentTestsPassingNormalized,
                                ]
                            ),
                            'assertionsTestRatio' => $assertionsTestRatio,
                            'testsMethodRatio' => $testsMethodRatio,
                            'percentTestsPassing' => $percentTestsPassing,
                        ],
                        'stats' => [
                            'tests' => $tests,
                            'assertions' => $junit['totals']['assertions'],
                            'files' => $cloverXML['files'],
                            'time' => $junit['totals']['time'],
                            'lines' => $coverageXML['lines'],
                            'methods' => $coverageXML['methods'],
                            'classes' => $coverageXML['classes'],
                            'elements' => $cloverXML['elements'],
                        ],
                        'classes' => [],
                        'tests' => $junit['tests'],
                    ];

                    // compute the index classes
                    $this->computeClasses('index', $data, $thresholds);

                    // compute analysis classes
                    foreach (array_keys($data['analysis']) as $key) {
                        $this->computeClasses($key, $data, $thresholds);
                    }

                    // compute stats classes
                    foreach (array_keys($data['stats']) as $key) {
                        $this->computeClasses($key, $data, $thresholds);
                    }

                    // compile testability file
                    $indexHtml = $pugEngine->renderFile(
                        $templatePath . 'testability.pug',
                        $data
                    );
                    $this->writeFile($reportPath . 'testability.html', $indexHtml);

                    // write to output
                    $passable->getOutputInterface()
                        ->writeln(
                            Carbon::now() . ' Compiled testability template: ' . $templatePath . 'testability.html'
                        );

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
     * This function computes the appropriate classes for each of the values
     *
     * @param string $key
     * @param array $data
     * @param stdClass $thresholds
     */
    private function computeClasses(string $key, array &$data, stdClass $thresholds)
    {
        if ($key !== 'time' && $key !== 'files') {
            $stats = [
                'tests',
                'assertions',
                'lines',
                'methods',
                'classes',
                'elements',
            ];

            switch (in_array($key, $stats)) {
                case true:
                    $percentageStats = [
                        'lines',
                        'methods',
                        'classes',
                        'elements',
                    ];

                    switch (in_array($key, $percentageStats)) {
                        case true:
                            $value = $data['stats'][$key]['percent'];

                            break;
                        case false:
                            $value = $data['stats'][$key];

                            break;
                    }

                    break;
                case false:
                    $value = $data['analysis'][$key];

                    break;
            }

            $data['classes'][$key] = [
                'backgroundClass' => $thresholds->classes->low->background_class,
                'iconClass' => $thresholds->classes->low->icon_class,
            ];

            if ($value >= $thresholds->testability->$key->low) {
                $data['classes'][$key] = [
                    'backgroundClass' => $thresholds->classes->medium->background_class,
                    'iconClass' => $thresholds->classes->medium->icon_class,
                ];
            }

            if ($value >= $thresholds->testability->$key->high) {
                $data['classes'][$key] = [
                    'backgroundClass' => $thresholds->classes->high->background_class,
                    'iconClass' => $thresholds->classes->high->icon_class,
                ];
            }
        }
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

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

                    $data = [
                        'route' => 'testability',
                        'title' => 'Testability',
                        'subject' => 'Code coverage and test analysis.',
                        'analysis' => [
                            'assertionsTestRatio' => $this->formatRatio(
                                $junit['totals']['assertions'],
                                $junit['totals']['tests']['number']
                            ),
                            'testsMethodRatio' => $this->formatRatio(
                                $junit['totals']['tests']['number'],
                                $coverageXML['methods']['executable']
                            ),
                            'coverageTestRatio' => $this->formatRatio(
                                $cloverXML['totals']['coverage'],
                                $junit['totals']['tests']['number']
                            ),
                        ],
                        'stats' => [
                            'tests' => $junit['totals']['tests'],
                            'assertions' => $junit['totals']['assertions'],
                            'coverage' => $cloverXML['totals']['coverage'],
                            'time' => $junit['totals']['time'],
                            'lines' => $coverageXML['lines'],
                            'methods' => $coverageXML['methods'],
                            'classes' => $coverageXML['classes'],
                            'traits' => $coverageXML['traits'],
                        ]
                    ];

                    // compile testability file
                    $indexHtml = $pugEngine->renderFile(
                        $templatePath . 'testability.pug',
                        $data
                    );
                    $this->writeFile($reportPath . 'testability.html', $indexHtml);

                    // write to output
                    $passable->getOutputInterface()
                        ->writeln(Carbon::now() . ' Compiled testability template: ' . $templatePath . '/testability.html');

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

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
use Exception;
use SimpleXMLElement;
use Throwable;

/**
 * Class ReadCoverageXMLReport
 * @package emPHPasis\Pipelines\Pipes\Generate\PHPUnit
 */
class ReadCoverageXMLReport extends Pipe
{
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

                    $xml = simplexml_load_file($decodedConfig->reports->phpunit->coverage_xml);

                    $rootTotals = $xml->project->directory->totals;

                    $reportData['phpunit']['coverageXML'] = [
                        'lines' => [
                            'executable' => $rootTotals->lines->attributes()->executable,
                            'executed' => $rootTotals->lines->attributes()->executed,
                            'percent' => number_format((float)$rootTotals->lines->attributes()->percent, 2),
                        ],
                        'methods' => [
                            'executable' => $rootTotals->methods->attributes()->count,
                            'executed' => $rootTotals->methods->attributes()->tested,
                            'percent' => number_format((float)$rootTotals->methods->attributes()->percent, 2),
                        ],
                        'classes' => [
                            'executable' => $rootTotals->classes->attributes()->count,
                            'executed' => $rootTotals->classes->attributes()->tested,
                            'percent' => number_format((float)$rootTotals->classes->attributes()->percent, 2),
                        ],
                        'traits' => [
                            'executable' => $rootTotals->traits->attributes()->count,
                            'executed' => $rootTotals->traits->attributes()->tested,
                            'percent' => number_format((float)$rootTotals->traits->attributes()->percent, 2),
                        ],
                    ];

                    $passable->setReportData($reportData);

                    $passable->getOutputInterface()
                        ->writeln(Carbon::now() . ' Read phpunit coverage xml report');

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
}

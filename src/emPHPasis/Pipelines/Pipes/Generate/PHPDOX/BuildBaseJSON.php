<?php
/**
 * Created by PhpStorm.
 * User: MrSir
 * Date: 3/10/2018
 * Time: 1:40 PM
 */

namespace emPHPasis\Pipelines\Pipes\Generate\PHPDOX;

use Carbon\Carbon;
use Closure;
use emPHPasis\Exceptions\Pipelines\Pipes\Generate\GatherReportsException;
use emPHPasis\Pipelines\Passables\Generate;
use emPHPasis\Pipelines\Pipes\Pipe;
use Exception;
use SimpleXMLElement;
use Throwable;

/**
 * Class BuildBaseJSON
 * @package emPHPasis\Pipelines\Pipes\Generate\PHPDOX
 */
class BuildBaseJSON extends Pipe
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
                    $reportPath = $decodedConfig->configurations->report_path;

                    $xml = simplexml_load_file($decodedConfig->reports->phpdox);

                    $sourceArray = $this->parseDirectory($xml->dir);
                    $sourceJSON = json_encode($sourceArray, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

                    $file = $reportPath . 'source.json';
                    $this->writeFile($file, $sourceJSON);

                    $passable->getOutputInterface()
                        ->writeln(Carbon::now() . ' Build base json: ' . $file);

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
     * @param SimpleXMLElement $xml
     *
     * @return array
     */
    public function parseDirectory(SimpleXMLElement $xml): array
    {
        $directory = [
            'name' => (string)$xml->attributes()->name,
            'children' => [],
        ];

        // recurse over sub directories
        foreach ($xml->dir as $directoryXML) {
            $directory['children'][] = $this->parseDirectory($directoryXML);
        }

        // recurse over sub directories
        foreach ($xml->file as $fileXML) {
            $directory['children'][] = $this->parseFile($fileXML);
        }

        return $directory;
    }

    /**
     * @param SimpleXMLElement $xml
     *
     * @return array
     */
    public function parseFile(SimpleXMLElement $xml): array
    {
        $file = [
            'name' => (string)$xml->attributes()->name,
            'size' => 500,
        ];

        return $file;
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
    public function writeFile(string $file, string $data): bool
    {
        return file_put_contents(
            $file,
            $data
        );
    }
}

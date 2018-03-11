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
use emPHPasis\Exceptions\Pipelines\Pipes\Generate\FindConfigException;
use emPHPasis\Pipelines\Passables\Generate;
use emPHPasis\Pipelines\Pipes\Pipe;
use Exception;
use Throwable;

/**
 * Class FindConfig
 * @package emPHPasis\Pipelines\Pipes\Generate
 */
class FindConfig extends Pipe
{
    /**
     * FindConfig constructor.
     */
    public function __construct()
    {
        parent::__construct(FindConfigException::class);
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
            // grab the config path
            $config = $passable->getConfig();

            // set the failure code and result
            $code = 500;
            $result = ['errors' => ['File does0\'t exist.']];

            // check if the config file exists
            if (file_exists($config)) {
                // read the config file and decode it
                $configContents = file_get_contents($config);
                $decodedContents = json_decode($configContents);

                // store the read values in the passable
                $passable->setDecodedConfig($decodedContents);

                $passable->getOutputInterface()
                    ->writeln(Carbon::now() . ' Found config file.');

                // set the successful code and result
                $code = 200;
                $result = ['message' => ['Success.']];
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

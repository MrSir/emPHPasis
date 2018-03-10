<?php
/**
 * Created by PhpStorm.
 * User: MrSir
 * Date: 3/10/2018
 * Time: 1:40 PM
 */

namespace emPHPasis\Pipelines\Pipes\Initialize;

use Closure;
use emPHPasis\Exceptions\Pipelines\Pipes\Initialize\CreateConfigException;
use emPHPasis\Pipelines\Passables\Initialize;
use emPHPasis\Pipelines\Pipes\Pipe;
use Exception;
use Throwable;

/**
 * Class CreateConfig
 * @package emPHPasis\Pipelines\Pipes\Initialize
 */
class CreateConfig extends Pipe
{
    /**
     * CreateConfig constructor.
     */
    public function __construct()
    {
        parent::__construct(CreateConfigException::class);
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

            // skip the pipe if the previous has failed
            if ($code == 200) {
                // grab the config information from the passable
                $path = $passable->getPath();
                $fileName = $passable::DEFAULT_CONFIG_FILE;
                $config = $passable->getConfig();
                $jsonConfig = json_encode(
                    $config,
                    JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
                );

                // set the failure results
                $code = 500;
                $result = ['errors' => 'Failed to write file.'];

                // write the file
                $writeFileResult = $this->writeFile(
                    $path . $fileName,
                    $jsonConfig
                );

                // assert for successful file creation
                if ($writeFileResult) {
                    $code = 200;
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

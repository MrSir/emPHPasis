<?php
/**
 * Created by PhpStorm.
 * User: MrSir
 * Date: 3/10/2018
 * Time: 1:40 PM
 */

namespace emPHPasis\Pipelines\Pipes\Initialize;

use Closure;
use emPHPasis\Exceptions\Pipelines\Pipes\Initialize\GenerateBaseConfigException;
use emPHPasis\Pipelines\Passables\Initialize;
use emPHPasis\Pipelines\Pipes\Pipe;
use Exception;
use Throwable;

/**
 * Class GenerateBaseConfig
 * @package emPHPasis\Pipelines\Pipes\Initialize
 */
class GenerateBaseConfig extends Pipe
{
    /**
     * GenerateBaseConfig constructor.
     */
    public function __construct()
    {
        parent::__construct(GenerateBaseConfigException::class);
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
            // initialize the config
            $config = [
                'version' => 'v1.0.0',
                'project' => 'My Project',
                'authors' => [
                    'My Name',
                ],
                'license' => 'MIT',
                'reports' => [],
                'configurations' => [],
            ];

            // set the config
            $passable->setConfig($config);

            // set the successful code and result
            $code = $passable::SUCCESS_CODE;
            $result = ['message' => 'Success'];

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

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
                    'directory' => 'build/emPHPasis/',
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

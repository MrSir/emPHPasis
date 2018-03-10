<?php
/**
 * Created by PhpStorm.
 * User: mtochev
 * Date: 5/23/2017
 * Time: 9:57 AM
 */

namespace emPHPasis\Pipelines\Pipes\Exception;

use Closure;
use Exception;

/**
 * Class Log
 * @package emPHPasis\Pipelines\Pipes
 */
class Log
{
    /**
     * @param Exception $e
     * @param Closure   $next
     *
     * @return mixed
     */
    public function handle(Exception $e, Closure $next)
    {
        //TODO implement logger

        return $next($e);
    }
}

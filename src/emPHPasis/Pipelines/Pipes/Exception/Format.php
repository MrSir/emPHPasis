<?php
/**
 * Created by PhpStorm.
 * User: MrSir
 * Date: 5/23/2017
 * Time: 9:57 AM
 */

namespace emPHPasis\Pipelines\Pipes\Exception;

use Closure;
use Exception;

/**
 * Class Format
 * @package emPHPasis\Pipelines\Pipes
 */
class Format
{
    /**
     * @param Exception $e
     * @param Closure   $next
     *
     * @return mixed
     */
    public function handle(Exception $e, Closure $next)
    {
        return $next(
            [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]
        );
    }
}
